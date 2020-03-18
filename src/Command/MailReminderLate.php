<?php


namespace App\Command;

use App\Entity\Borrowing;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class MailReminderLate extends Command
{
    protected static $defaultName = 'app:mail-reminder-late';

    private $em;
    private $mailer;
    private $twig;

    public function __construct(EntityManagerInterface $manager, \Swift_Mailer $mailer, Environment $twig)
    {
        parent::__construct();
        $this->em = $manager;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    protected function configure()
    {
        $this
            ->setDescription('Envoie un mail de rappel aux emprunteurs qui sont en retard.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $today = new \DateTime("now");
        $today->modify('- 1 day');
        $repo = $this->em->getRepository(Borrowing::class);
        $borrowings = $repo->findAll();

        foreach ($borrowings as $borrowing){
            if(($borrowing->getEndedOn() < $today) and ($borrowing->getInProgress())) {
                $email = $borrowing->getBorrowedBy()->getEmail();
                $message = (new \Swift_Message('[MyCentraleEmprunt] Retard dans le rendu d\'un emprunt'))
                    ->setFrom('ginfo@centrale-marseille.fr')
                    ->setTo($email)
                    ->setBody(
                        $this->twig->render(
                            'emails/MailReminderLate.html.twig',
                            ['borrowing' => $borrowing]
                        ),
                        'text/html'
                    );
                $this->mailer->send($message);
                echo('mail envoyé');
            }
        }

        $io->success('Tous les mails on été envoyés.');
    }
}