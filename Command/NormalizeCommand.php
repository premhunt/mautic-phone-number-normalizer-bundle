<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticPhoneNumberNormalizerBundle\Command;

use Mautic\CoreBundle\Command\ModeratedCommand;
use Mautic\LeadBundle\Deduplicate\Exception\SameContactException;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticDolistBundle\Dolist\Callback\CallbackEmail;
use MauticPlugin\MauticDolistBundle\Dolist\Callback\CallbackFactory;
use MauticPlugin\MauticDolistBundle\Dolist\Callback\CallbackService;
use MauticPlugin\MauticEcrBundle\Integration\EcrSettings;
use MauticPlugin\MauticEcrBundle\Sync\DAO\InputDAO;
use MauticPlugin\MauticEcrBundle\Sync\EcrSync;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Model\ContactPhoneNumberModel;
use MauticPlugin\MauticPhoneNumberNormalizerBundle\Service\PhoneNumberNormalizer;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Translation\TranslatorInterface;

class NormalizeCommand extends ModeratedCommand
{

    /**
     * @var PhoneNumberNormalizer
     */
    private $phoneNumberNormalizer;

    /**
     * @var ContactPhoneNumberModel
     */
    private $contactPhoneNumberModel;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * NormalizeCommand constructor.
     *
     * @param PhoneNumberNormalizer   $phoneNumberNormalizer
     * @param ContactPhoneNumberModel $contactPhoneNumberModel
     * @param LeadModel               $leadModel
     * @param TranslatorInterface     $translator
     */
    public function __construct(
        PhoneNumberNormalizer $phoneNumberNormalizer,
        ContactPhoneNumberModel $contactPhoneNumberModel,
        LeadModel $leadModel,
        TranslatorInterface $translator
    ) {
        parent::__construct();
        $this->phoneNumberNormalizer   = $phoneNumberNormalizer;
        $this->contactPhoneNumberModel = $contactPhoneNumberModel;
        $this->leadModel               = $leadModel;
        $this->translator              = $translator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mautic:phone:number:normalize')
            ->setDescription('Normalize phone numbers')
            ->addOption(
                'batch-limit',
                '-b',
                InputOption::VALUE_OPTIONAL,
                'Set batch size of contacts to process per round. Defaults to 100.',
                100
            )
            ->addOption(
                'dry-run',
                'r',
                InputOption::VALUE_NONE,
                'Do a dry run for one batch without modify anything.'
            )
            ->setHelp('This command normalize phone numbers based on plugin settings');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = __CLASS__;
        if (!$this->checkRunStatus($input, $output, $key)) {
            return 0;
        }

        $dryRun = $input->getOption('dry-run');

        $totalContacts = $this->contactPhoneNumberModel->getPhoneNumberContactCount();

        $lastContactId = 0;
        $modified      = 0;
        $start         = 0;
        $batchLimit    = $input->getOption('batch-limit');

        if ($dryRun) {
            $table = $this->generateTableHeader($output);
        }else{
            $progress      = new ProgressBar($output, $totalContacts);
        }

        while (true) {
            $contacts = $this->contactPhoneNumberModel->getNextPhoneNumberContacts(
                $lastContactId,
                $start,
                $batchLimit
            );
            if (empty($contacts)) {
                break;
            }
            foreach ($contacts as $contact) {

                if ($dryRun) {
                    $row = $this->generateTableRow($contact);
                    array_push($row, $contact->getLeadPhoneNumber());
                }

                $this->phoneNumberNormalizer->normalize($contact);

                if ($dryRun) {
                    array_push($row, $contact->getLeadPhoneNumber());
                    $table->addRow($row);
                }else{
                    $progress->advance();
                }

                if (!empty($contact->getChanges())) {
                    $modified++;
                }
            }

            if ($dryRun) {
                $table->render();

                $helper   = $this->getHelperSet()->get('question');
                $question = new ConfirmationQuestion(
                    '<info>'.$this->translator->trans('mautic.phonenumbernormalizer.command.continue.next.batch').'</info> ', false
                );

                if (!$helper->ask($input, $output, $question)) {
                    break;
                }

            } else {
                $this->leadModel->saveEntities($contacts);
                $this->leadModel->getRepository()->clear();
            }
            $start = $start + $batchLimit;

        }
        if (!$dryRun) {
            $progress->finish();
        }


        $output->writeln('');
        $output->writeln($this->translator->trans('mautic.phonenumbernormalizer.modified', ['%count%' => $modified]));

        return 0;
    }

    /**
     * @param OutputInterface $output
     *
     * @return Table
     */
    private function generateTableHeader(OutputInterface $output)
    {
        $table = new Table($output);
        $table
            ->setHeaders(
                array_merge(
                    [
                        $this->translator->trans('mautic.lead.report.contact_id'),
                        $this->translator->trans('mautic.lead.contact'),
                        $this->translator->trans('mautic.phonenumbernormalizer.before'),
                        $this->translator->trans('mautic.phonenumbernormalizer.after'),
                    ]
                )
            );

        return $table;
    }

    /**
     * @param Lead $contact
     *
     * @return array
     */
    private function generateTableRow(Lead $contact)
    {
        return
            [
                $contact->getId(),
                $contact->getPrimaryIdentifier(),
            ];
    }

}
