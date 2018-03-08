<?php
/**
 * Created by Afroze.S.
 * Date: 14/2/18
 * Time: 3:35 PM
 */

namespace Twentyone\UpdateStock\Console;


use MagentoEnv\Entity\ConfigEnv;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twentyone\UpdateStock\ServiceEntity\SoapEntity;
use Magento\Framework\Event\ObserverInterface;

class TestCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SoapEntity
     */
    private $soapEntity;
    /**
     * @var ConfigEnv
     */
    private $configEnv;

    /**
     * TestCommand constructor.
     * @param LoggerInterface $logger
     * @param ConfigEnv $configEnv
     * @param SoapEntity $soapEntity
     */
    public function __construct(LoggerInterface $logger,
                                ConfigEnv $configEnv,
                                SoapEntity $soapEntity)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->soapEntity = $soapEntity;
        $this->configEnv = $configEnv;
    }

    /**
     * Configure console command and arguments and options required
     */
    protected function configure() {
        $this->setName('Twentyone:UpdateStock');
        $this->setDescription('Update products from atelier file');
        $this->setHelp("This command helps to update products from atelier CSV");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln("testing soap");
        $this->soapEntity->setConnection($this->configEnv->getEnv('atelier_url'));
        $params = array(
            'ID_ARTICOLO' => 60542,
            'TAGLIA' => '48'
        );

        //'DisponibilitaVarianteTaglia', [60542, '48']
        $output->writeln("Disponibilita Variante Taglia");
        $res = $this->soapEntity->callFunction('DisponibilitaVarianteTaglia', array($params));
        $output->writeln(json_encode($res));

        $output->writeln("Disponibilita Variante");
        $res = $this->soapEntity->callFunction('DisponibilitaVariante', array($params));
        $output->writeln(json_encode($res));
        /*
        $output->writeln("Update client");
        $res = $this->soapEntity->updateClient('oriana.potente@gmail.com', 'Oriana','Potente');
        var_dump($res->AggiornaClienteCompletaFidelityResult);
        $output->writeln("Update order");
        $res = $this->soapEntity->updateOrder('oriana.potente@gmail.com', 60542,'48',123, 'Via Roma 1', '10100, Torino, TO', 'Italia', 10, 1);
        var_dump($res->SetImpegnoEstesoResult);

        */

        $output->writeln("Impegno Spedizone");
        $res = $this->soapEntity->communicateShippingFare('oriana.potente@gmail.com',123, 6.2);
        $output->writeln(json_encode($res));
        $res = $this->soapEntity->communicateOrderStatus('oriana.potente@gmail.com', 123, 'complete');
        $output->writeln(json_encode($res));
        $res = $this->soapEntity->communicateOrderPayment('oriana.potente@gmail.com',123, 'paypal');
        $output->writeln(json_encode($res));



        $output->writeln("testing soap end");
    }
}
