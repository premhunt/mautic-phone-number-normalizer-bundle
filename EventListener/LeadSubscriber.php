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

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Model\NotificationModel;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\UserBundle\Model\UserModel;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Integration\PhoneNumberNormalizerSettings;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Service\PhoneNumberNormalizer;

class LeadSubscriber extends CommonSubscriber
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
            LeadEvents::LEAD_PRE_SAVE => ['onLeadPreSave', 0],
        ];
    }

    /**
     * @param LeadEvent $event
     */
    public function onLeadPreSave(LeadEvent $event)
    {
        $contact = $event->getLead();
        if ($this->phoneNumberNormalizer->getPhoneNumberNormalizerSettings()->beforeFieldChange()) {
            if ($this->hasChangedFieldForNormalize(
                $contact,
                $this->phoneNumberNormalizer->getPhoneNumberNormalizerSettings()->getPhoneFields()
            )) {
                $this->phoneNumberNormalizer->normalize($contact);
            }
        }
    }

    /**
     * @param Lead $contact
     * @param array $fields
     *
     * @return bool
     */
    private function hasChangedFieldForNormalize(Lead $contact, array $fields)
    {
        $changes = $contact->getChanges();
        if (!empty($fields) && !empty($changes)) {
            foreach ($fields as $field) {
                if (isset($changes[$field])) {
                    return true;
                }
            }
        }

        return false;
    }

}
