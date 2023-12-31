const ghpages = require('gh-pages')
const execSync = require('child_process').execSync;

// Makes the script crash on unhandled rejections instead of silently
// ignoring them. In the future, promise rejections that are not handled will
// terminate the Node.js process with a non-zero exit code.
process.on('unhandledRejection', err => {
  throw err;
});

function genDocs() {
  try {
    execSync('gitbook build', { stdio: 'ignore' });
    return true;
  } catch (e) {
    return false;
  }
}

function uploadDocs() {
  try {
    ghpages.publish('_book', {
      branch: 'oa-pages',
      message: 'Auto-generated commit',
      repo: 'http://git.code.oa.com/ads/spaphp.git'
    }, () => {});
  } catch (e) {
    return false;
  }
}
genDocs()
uploadDocs()

