<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticPhoneNumberNormalizerBundle\EventListener;

//use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Mautic\CoreBundle\Model\NotificationModel;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\SmsBundle\Event\SmsSendEvent;
use Mautic\SmsBundle\SmsEvents;
use Mautic\UserBundle\Model\UserModel;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Integration\PhoneNumberNormalizerSettings;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Service\PhoneNumberNormalizer;

class SmsSubscriber implements EventSubscriberInterface
{

    /**
     * @var PhoneNumberNormalizer
     */
    private $phoneNumberNormalizer;

    /**
     * LeadSubscriber constructor.
     *
     * @param PhoneNumberNormalizer $phoneNumberNormalizer
     */
    public function __construct(PhoneNumberNormalizer $phoneNumberNormalizer)
    {
        $this->phoneNumberNormalizer = $phoneNumberNormalizer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SmsEvents::SMS_ON_SEND => ['onSmsSend', 0],
        ];
    }

    /**
     * @param SmsSendEvent $event
     */
    public function onSmsSend(SmsSendEvent $event)
    {
        if ($this->phoneNumberNormalizer->getPhoneNumberNormalizerSettings()->beforeSmsSend()) {
            $contact = $event->getLead();
            $this->phoneNumberNormalizer->normalize($contact);
        }
    }

}
