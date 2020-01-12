<?php

/*
 * @copyright   2019 MTCExtendee. All rights reserved
 * @author      MTCExtendee
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticPhoneNumberNormalizerBundle\Integration;

use Mautic\CoreBundle\Form\Type\SortableListType;
use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\LeadBundle\Form\Type\LeadFieldsType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;


class PhoneNumberNormalizerIntegration extends AbstractIntegration
{
    const INTEGRATION_NAME         = 'PhoneNumberNormalizer';

    const INTEGRATION_DISPLAY_NAME = 'Phone Number Normalizer';

    public function getName()
    {
        return self::INTEGRATION_NAME;
    }

    public function getDisplayName()
    {
        return self::INTEGRATION_DISPLAY_NAME;
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function getRequiredKeyFields()
    {
        return [
        ];
    }

    public function getIcon()
    {
        return 'plugins/MauticPhoneNumberNormalizerBundle/Assets/img/icon.png';
    }



    /**
     * @param \Mautic\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ($formArea == 'features') {

            $builder->add(
                'phone_fields',
                LeadFieldsType::class,
                [
                    'label'       => 'mautic.phonenumbernormalizer.form.phone_fields',
                    'label_attr'  => ['class' => 'control-label'],
                    'attr'        => [
                        'class' => 'form-control',
                    ],
                    'required'    => true,
                    'empty_value' => '',
                    'multiple'    => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );

            $builder->add(
                'region_rules',
                SortableListType::class,
                [
                    'with_labels'      => true,
                    'label'            => 'mautic.phonenumbernormalizer.form.start_with',
                    'add_value_button' => 'mautic.phonenumbernormalizer.form.add',
                    'option_notblank'  => false,
                    'option_required'  => false,
                    'key_value_pairs'  => true,
                    'required'         => false,

                ]
            );

            $builder->add(
                'before_field_change',
                YesNoButtonGroupType::class,
                [
                    'label' => 'mautic.phonenumbernormalizer.form.before_field_change',
                    'data'  => (isset($data['before_field_change'])) ? (bool) $data['before_field_change'] : false,
                    'attr'  => [
                    ],
                ]
            );

            $builder->add(
                'before_sms_send',
                YesNoButtonGroupType::class,
                [
                    'label' => 'mautic.phonenumbernormalizer.form.before_sms_send',
                    'data'  => (isset($data['before_sms_send'])) ? (bool) $data['before_sms_send'] : false,
                    'attr'  => [
                    ],
                ]
            );
        }
    }
}
