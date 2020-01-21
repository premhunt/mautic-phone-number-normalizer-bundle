<?php

/*
 * @copyright   2019 MTCExtendee. All rights reserved
 * @author      MTCExtendee
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticPhoneNumberNormalizerBundle\Model;

use Doctrine\ORM\EntityManager;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\LeadRepository;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Integration\PhoneNumberNormalizerSettings;

class ContactPhoneNumberModel
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var array
     */
    private $phoneFields;

    /**
     * ContactPhoneNumberModel constructor.
     *
     * @param EntityManager                 $entityManager
     * @param LeadRepository                $leadRepository
     * @param PhoneNumberNormalizerSettings $phoneNumberNormalizerSettings
     */
    public function __construct(
        EntityManager $entityManager,
        LeadRepository $leadRepository,
        PhoneNumberNormalizerSettings $phoneNumberNormalizerSettings
    ) {
        $this->entityManager  = $entityManager;
        $this->leadRepository = $leadRepository;
        $this->phoneFields = $phoneNumberNormalizerSettings->getPhoneFields();
    }

    /**
     * @return int
     */
    public function getPhoneNumberContactCount()
    {
        $qb = $this->getBasedQuery();
        $qb->select('count(*)');

        return (int) $qb->execute()->fetchColumn();
    }


    /**
     * @param       $lastId
     * @param       $start
     * @param       $max
     *
     * @return array|Lead[]
     */
    public function getNextPhoneNumberContacts($lastId, $start, $max)
    {
        $qb = $this->getBasedQuery();
        $qb->select("l.id")
            ->andWhere($qb->expr()->gt('l.id', ':lastId'))
            ->setParameter('lastId', $lastId)
            ->setFirstResult($start)
            ->setMaxResults($max)
            ->orderBy("l.id");

        $nexts = $qb->execute()->fetchAll();
        return $this->leadRepository->getEntities(
            [
                'filter'           => [
                    'force' => [
                        [
                            'column' => 'l.id',
                            'expr'   => 'in',
                            'value'  => array_column($nexts, 'id'),
                        ],
                    ],
                ],
                'ignore_paginator' => true,
            ]
        );

    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getBasedQuery()
    {
        $qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->from(MAUTIC_TABLE_PREFIX.'leads', 'l');

        foreach ($this->phoneFields as $phoneField) {
            $qb->orWhere($qb->expr()->andX(
                $qb->expr()->isNotNull('l.'.$phoneField),
                $qb->expr()->neq('l.'.$phoneField, $qb->expr()->literal(''))
            ));
        }

        return $qb;
    }

}
