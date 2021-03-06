<?php
/**
 * BEAR.Framework
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Framework\Module\Provider as Provide;

use BEAR\Framework\Inject\TmpDirInject;

/**
 * Twig
 *
 * @see http://twig.sensiolabs.org/
 */
class TwigProvider implements Provide
{
    use TmpDirInject;

    /**
     * @return array
     */
    public function get()
    {
        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem('/'),
            [
                'cache' => $this->tmpDir . '/tmp/twig',
                'auto_reload' => true
            ]
        );

        return $twig;
    }
}
