<?php

namespace Xibo\Xmds\Listeners;

use Xibo\Event\XmdsDependencyListEvent;
use Xibo\Event\XmdsDependencyRequestEvent;
use Xibo\Factory\FontFactory;
use Xibo\Listener\ListenerLoggerTrait;

class XmdsFontsListener
{
    use ListenerLoggerTrait;

    /**
     * @var FontFactory
     */
    private $fontFactory;

    public function __construct(FontFactory $fontFactory)
    {
        $this->fontFactory = $fontFactory;
    }

    public function onDependencyList(XmdsDependencyListEvent $event)
    {
        $this->getLogger()->debug('onDependencyList: XmdsFontsListener');

        foreach ($this->fontFactory->query() as $font) {
            $event->addDependency(
                'font',
                $font->id,
                'fonts/'.$font->fileName,
                $font->size,
                $font->md5,
                true
            );
        }
        $fontsCssPath = PROJECT_ROOT . '/library/fonts/fonts.css';

        $event->addDependency(
            'fontCss',
            1,
            'fonts/fonts.css',
            filesize($fontsCssPath),
            md5($fontsCssPath),
            true
        );
    }

    public function onDependencyRequest(XmdsDependencyRequestEvent $event)
    {
        $this->getLogger()->debug('onDependencyRequest: XmdsFontsListener');

        if ($event->getFileType() === 'font') {
            $font = $this->fontFactory->getById($event->getId());
            $event->setRelativePathToLibrary('/fonts/' . $font->fileName);
        } else if ($event->getFileType() === 'fontCss') {
            $event->setRelativePathToLibrary('/fonts/fonts.css');
        }
    }
}