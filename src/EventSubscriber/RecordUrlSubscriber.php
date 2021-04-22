<?php

namespace App\EventSubscriber;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class RecordUrlSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * RecordUrlSubscriber constructor.
     * @param ContainerBagInterface $params
     */
    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'class' => 'App\\Entity\\Record', // if no class, subscribe to every serialization
                'format' => 'json', // optional format
                'priority' => 0, // optional priority
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $url = $this->params->get('app.records_path') . "/" . $event->getObject()->getName();
        $event->getVisitor()->visitProperty(new StaticPropertyMetadata ('', 'url', null), $url);
    }
}
