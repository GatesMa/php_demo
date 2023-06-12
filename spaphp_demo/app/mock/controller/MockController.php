<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/6/11
 * Time: 下午1:58
 */

namespace mock\controller;

use spaphp\metadata\MetadataFactory;
use spaphp\generator\Generator;
use spaphp\metadata\tags\ParamTag;
use spaphp\serializer\JsonSerializer;
use spaphp\validation\Validator;

/**
 * Class MockController
 *
 * @package mock\controller
 */
class MockController extends Controller
{
    /**
     * @param string $path
     *
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    public function __invoke(string $path)
    {
        // routing
        $httpMethod = $this->app->request->getMethod();
        $uri = '/' . $path;
        $route = $this->app->router->dispatch($httpMethod, $uri);
        $action = $route->getAction();
        $vars = $route->getAttributes();
        $request = $this->app->request;
        $request->add($vars);
        if ($action instanceof \Closure) {
            throw new \RuntimeException("mock server is not support Closure route");
        }

        list($target, $method) = explode('@', $action);

        /**
         * @var MetadataFactory $metadataFactory
         */
        $metadataFactory = $this->app->make(MetadataFactory::class);
        $metadataFactory->setCache();

        /**
         * @var JsonSerializer $serializer
         */
        $serializer = $this->app->make(JsonSerializer::class);
        $serializer->setMetaFactory($metadataFactory);

        /**
         * @var Validator $validator
         */
        $validator = $this->app->make(Validator::class);
        $serializer->setValidator($validator);

        /**
         * @var Generator $generator
         */
        $generator = $this->app->make(Generator::class);
        $generator->setMetadataFactory($metadataFactory);

        $classMetadata = $metadataFactory->getClassMetadata($target);
        if (!isset($classMetadata->methods[$method])) {
            throw new \InvalidArgumentException('method [' . $method . '] not exists in [' . $target . ']');
        }
        $methodMetadata = $classMetadata->methods[$method];

        // validate parameters
        $input = [];
        $errors = [];

        /**
         * @var ParamTag $paramTag
         */
        foreach ($methodMetadata->params as $paramTag) {
            $variableName = $paramTag->getVariable();
            $source = $paramTag->getBind();

            if (strpos($source, 'query.') === 0) {
                $name = substr($source, strlen('query.'));
                $data = $request->get($name);
            } elseif (strpos($source, 'cookie.') === 0) {
                $name = substr($source, strlen('cookie.'));
                $data = $request->getCookie($name);
            } elseif (strpos($source, 'header.') === 0) {
                $name = substr($source, strlen('header.'));
                $data = $request->getHeader($name);
            } elseif (strpos($source, 'file.') === 0) {
                $name = substr($source, strlen('file.'));
                $data = $request->getFile($name);
            } elseif ($source == 'request') {
                $data = $request->getPost();
            } else {
                if (strpos($source, 'request.') === 0) {
                    $name = substr($source, strlen('request.'));
                } else {
                    $name = $paramTag->getVariable();
                }

                $data = $request->get($name);
            }

            try {
                $input[] = $serializer->make(
                    $data,
                    $paramTag->getType(),
                    $paramTag->getAsserts(),
                    $paramTag->getError(),
                    $variableName
                );
            } catch (\InvalidArgumentException $e) {
                $errors[] = [
                    'parameter' => $variableName,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // generate response
        $returnType = $methodMetadata->return->type;
        $return = $generator->generate($returnType);

        $ret = [
            'ret' => count($errors) > 0 ? 1 : 0,
            'errors' => $errors,
            'data' => $return,
        ];

        return $ret;
    }

}
