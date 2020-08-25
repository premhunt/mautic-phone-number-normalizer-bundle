<?php

return [
    'name'        => 'MauticPhoneNumberNormalizerBundle',
    'description' => 'Phone number normalizer for Mautic',
    'version'     => '1.0.0',
    'author'      => 'MTCExtendee',

    'routes' => [
    ],

    'services'   => [
        'events'       => [
            'mautic.phonenumbernormalizer.sms.subscriber' => [
                'class'     => \MauticPlugin\MauticPhoneNumberNormalizerBundle\EventListener\SmsSubscriber::class,
                'arguments' => [
                    'mautic.phonenumbernormalizer.service.normalizer'
                ],
            ],
            'mautic.phonenumbernormalizer.lead.subscriber' => [
                'class'     => \MauticPlugin\MauticPhoneNumberNormalizerBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'mautic.phonenumbernormalizer.service.normalizer'
                ],
            ],

        ],
        'forms'        => [
        ],
        'models'       => [

        ],
        'integrations' => [
            'mautic.integration.phonenumbernormalizer' => [
                'class'     => \MauticPlugin\MauticPhoneNumberNormalizerBundle\Integration\PhoneNumberNormalizerIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                    'mautic.helper.integration',
                ],
            ],
        ],
        'others'       => [
            'mautic.phonenumbernormalizer.service.normalizer' => [
                'class'     => \MauticPlugin\MauticPhoneNumberNormalizerBundle\Service\PhoneNumberNormalizer::class,
                'arguments' => [
                    'mautic.lead.model.lead',
                    'mautic.phonenumbernormalizer.integration.settings'
                ],
            ],
            'mautic.phonenumbernormalizer.model.contact_phone_number' => [
                'class'     => \MauticPlugin\MauticPhoneNumberNormalizerBundle\Model\ContactPhoneNumberModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'mautic.lead.repository.lead',
                    'mautic.phonenumbernormalizer.integration.settings'
                ],
            ],
            'mautic.phonenumbernormalizer.integration.settings' => [
                'class'     => \MauticPlugin\MauticPhoneNumberNormalizerBundle\Integration\PhoneNumberNormalizerSettings::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
        ],
        'controllers'  => [
        ],
        'commands'     => [
            'mautic.phonenumbernormalizer.command.normalize' => [
                'class'     => \MauticPlugin\MauticPhoneNumberNormalizerBundle\Command\NormalizeCommand::class,
                'arguments' => [
                    'mautic.phonenumbernormalizer.service.normalizer',
                    'mautic.phonenumbernormalizer.model.contact_phone_number',
                    'mautic.lead.model.lead',
                    'translator'
                ],
                'tag'       => 'console.command',
            ],
        ],
    ],
    'parameters' => [
    ],
];
