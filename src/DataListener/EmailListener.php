<?php

namespace Labstag\DataListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Labstag\Entity\Email;
use Labstag\Entity\Templates;
use Labstag\Lib\EventSubscriberLib;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class EmailListener extends EventSubscriberLib
{

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, RouterInterface $router)
    {
        $this->container = $container;
        $this->router    = $router;
    }

    /**
     * Sur quoi écouter.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Email) {
            return;
        }

        $this->checkEmail($entity, $args);
    }

    private function checkEmail(Email $entity, $args)
    {
        $check = $entity->isChecked();
        if (true === $check) {
            return;
        }

        $search     = ['code' => 'checked-mail'];
        $manager    = $args->getEntityManager();
        $repository = $manager->getRepository(Templates::class);
        $templates  = $repository->findOneBy($search);
        $html       = $templates->getHtml();
        $text       = $templates->getText();
        $user       = $entity->getRefuser();
        $this->setConfigurationParam($args);
        $replace    = [
            '%site%'     => $this->configParams['site_title'],
            '%username%' => $user->getUsername(),
            '%email%'    => $entity->getAdresse(),
            '%url%'      => $this->router->generate(
                'check-email',
                [
                    'id' => $entity->getId(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        $html    = strtr($html, $replace);
        $text    = strtr($text, $replace);
        $message = new Swift_Message();
        $sujet   = str_replace(
            '%site%',
            $this->configParams['site_title'],
            $templates->getname()
        );
        $message->setSubject($sujet);
        $message->setFrom($user->getEmail());
        $message->setTo($this->configParams['site_no-reply']);
        $message->setBody($html, 'text/html');
        $message->addPart($text, 'text/plain');
        $mailer = $this->container->get('swiftmailer.mailer.default');
        $mailer->send($message);
    }
}
