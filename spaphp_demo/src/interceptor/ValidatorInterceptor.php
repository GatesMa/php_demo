<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/6/14
 * Time: 下午3:53
 */

namespace app\interceptor;

use Closure;
use spaphp\Application;
use spaphp\http\Request;
use spaphp\metadata\MetadataFactory;
use spaphp\metadata\tags\ParamTag;
use spaphp\metadata\tags\ReturnTag;
use spaphp\serializer\SerializerException;
use spaphp\validation\Validator;
use spaphp\serializer\JsonSerializer;

/**
 * Class ValidatorInterceptor
 *
 * @package app\interceptor
 */
class ValidatorInterceptor
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, \Closure $next)
    {
        $route = $request->currentRoute();
        $action = $route->getAction();
        if ($action instanceof Closure) {
            return $next($request);
        }

        if (!strpos($action, '@')) {
            $action .= '@__invoke';
        }
        list($class, $method) = explode('@', $action);

        /**
         * @var MetadataFactory $metadataFactory
         */
        $metadataFactory = $this->app->make(MetadataFactory::class);
        $validator = $this->app->make(Validator::class);

        /**
         * @var JsonSerializer $serializer
         */
        $serializer = $this->app->make(JsonSerializer::class);
        $serializer->setValidator($validator);

        $classMetadata = $metadataFactory->getClassMetadata($class);
        if (!isset($classMetadata->methods[$method])) {
            throw new \InvalidArgumentException('method "' . $method . '" not exists in [' . $class . ']');
        }
        $methodMetadata = $classMetadata->methods[$method];

        // validate parameters
        $input = [];
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
                throw new \InvalidArgumentException("Parameter [ $variableName ] " . $e->getMessage());
            }
        }

        // call
        $this->app->bindMethod($action, function ($controller, $app) use ($method, $input) {
            return $controller->$method(...$input);
        });

        $response = $next($request);

        // validate response
        /**
         * @var ReturnTag $returnTag
         */
        $returnTag = $methodMetadata->return;
        try {
            $response = $serializer->make($response, $returnTag->getType(), [], 'response');
        } catch (SerializerException $e) {
            throw $e;
        }

        return $response;
    }
}
