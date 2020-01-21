<?php

/*
 * @copyright   2019 MTCExtendee. All rights reserved
 * @author      MTCExtendee
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticPhoneNumberNormalizerBundle\Integration;

use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;

class PhoneNumberNormalizerSettings
{

    /**
     * @var bool|\Mautic\PluginBundle\Integration\AbstractIntegration
     */
    private $integration;

    private $enabled = false;

    /**
     * @var array
     */
    private $settings = [];

    /**
     * DolistSettings constructor.
     *
     * @param IntegrationHelper $integrationHelper
     */
    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integration = $integrationHelper->getIntegrationObject(PhoneNumberNormalizerIntegration::INTEGRATION_NAME);
        if ($this->integration instanceof PhoneNumberNormalizerIntegration && $this->integration->getIntegrationSettings(
            )->getIsPublished()) {
            $this->settings = $this->integration->mergeConfigToFeatureSettings();
            $this->enabled = true;
        }
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }


    /**
     * @return array
     */
    public function getPhoneFields()
    {
        return ArrayHelper::getValue('phone_fields', $this->settings, []);
    }

    /**
     * @return array
     */
    public function getRegionRules()
    {
        return ArrayHelper::getValue('region_rules', $this->settings, []);
    }

    /**
     * @return bool
     */
    public function beforeFieldChange()
    {
        return ArrayHelper::getValue('before_field_change', $this->settings, false);
    }


    /**
     * @return bool
     */
    public function beforeSmsSend()
    {
        return ArrayHelper::getValue('before_sms_send', $this->settings, false);
    }

}
