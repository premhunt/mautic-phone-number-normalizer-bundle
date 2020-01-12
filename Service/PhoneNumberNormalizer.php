<?php

/*
 * @copyright   2019 MTCExtendee. All rights reserved
 * @author      MTCExtendee
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticPhoneNumberNormalizerBundle\Service;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Exception\RegionRulesException;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Integration\PhoneNumberNormalizerSettings;

class PhoneNumberNormalizer
{
    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var PhoneNumberNormalizerSettings
     */
    private $phoneNumberNormalizerSettings;

    /**
     * @var Lead
     */
    private $contact;

    /**
     * @var PhoneNumberUtil
     */
    private $phoneUtil;

    /**
     * PhoneNumberNormalizer constructor.
     *
     * @param LeadModel                     $leadModel
     * @param PhoneNumberNormalizerSettings $phoneNumberNormalizerSettings
     */
    public function __construct(LeadModel $leadModel, PhoneNumberNormalizerSettings $phoneNumberNormalizerSettings)
    {
        $this->leadModel                     = $leadModel;
        $this->phoneNumberNormalizerSettings = $phoneNumberNormalizerSettings;
        $this->phoneUtil                     = PhoneNumberUtil::getInstance();
    }

    public function normalize($contact)
    {
        $this->contact = $contact;
        $fields        = $this->phoneNumberNormalizerSettings->getPhoneFields();
        foreach ($fields as $field) {
            $contactPhoneNumber = $this->contact->getFieldValue($field);
            if (empty($contactPhoneNumber)) {
                continue;
            }
            try {
                $formattedPhoneNumber = $this->normalizeDefaultRegionNumbers($contactPhoneNumber);
            } catch (NumberParseException $e) {
                try {
                    $formattedPhoneNumber = $this->normalizeBasedOnRegionRules($contactPhoneNumber);
                } catch (RegionRulesException $e) {
                    continue;
                }
            }
            if ($contactPhoneNumber != $formattedPhoneNumber) {
                $this->contact->addUpdatedField($field, $formattedPhoneNumber);
            }
            die($this->contact->getFieldValue($field));
        }

    }

    /**
     * @param string $contactPhoneNumber
     *
     * @return string
     * @throws NumberParseException
     */
    private function normalizeDefaultRegionNumbers($contactPhoneNumber)
    {
        $phoneNumber = $this->phoneUtil->parse($contactPhoneNumber, null);

        return $this->phoneUtil->format($phoneNumber, PhoneNumberFormat::E164);

    }

    /**
     * @param $contactPhoneNumber
     *
     * @return string
     * @throws RegionRulesException
     */
    private function normalizeBasedOnRegionRules($contactPhoneNumber)
    {
        $regionRules = $this->phoneNumberNormalizerSettings->getRegionRules();
        foreach ($regionRules as $regionRule => $region) {
            $regionRuleExplodes = explode('|', $regionRule);
            foreach ($regionRuleExplodes as $regionRuleExplode) {
                if (strpos($contactPhoneNumber, $regionRuleExplode) === 0) {
                    try {
                        $phoneNumber = $this->phoneUtil->parse($contactPhoneNumber, $region);

                        return $this->phoneUtil->format($phoneNumber, PhoneNumberFormat::E164);
                    } catch (NumberParseException $e) {

                    }
                }
            }
        }

        throw new RegionRulesException();
    }

    /**
     * @return PhoneNumberNormalizerSettings
     */
    public function getPhoneNumberNormalizerSettings(): PhoneNumberNormalizerSettings
    {
        return $this->phoneNumberNormalizerSettings;
    }

}
