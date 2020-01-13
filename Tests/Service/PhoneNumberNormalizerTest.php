<?php


/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressManipulatorBundle\Test\Sync\Domain;



use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticAddressManipulatorBundle\Exception\SkipMappingException;

class PhoneNumberNormalizerTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Lead
     */
    private $lead;

    protected function setUp()
   {
       parent::setUp();
       $this->lead = new Lead();
   }

}
