<?php
/**
 * Created by Afroze.S.
 * Date: 14/2/18
 * Time: 11:33 AM
 */

namespace Twentyone\UpdateStock\Observer;


use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MagentoEnv\Entity\ConfigEnv;
use Psr\Log\LoggerInterface;

class UpdateProductObserver implements ObserverInterface
{
    /**
     * @var Product
     */
    private $product;
    /**
     * @var ConfigEnv
     */
    private $configEnv;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UpdateStockObserver constructor.
     * @param LoggerInterface $logger
     * @param ConfigEnv $configEnv
     * @param Product $product
     */
    public function __construct(LoggerInterface $logger,
                                ConfigEnv $configEnv,
                                Product $product)
    {
        $this->product = $product;
        $this->configEnv = $configEnv;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $myEventData = null;
        $myEventData = $observer->getData('product');

        $this->logger->debug("event", ["save"]);
        $this->logger->debug("event", [$observer->getEventName()]);
        $this->logger->debug("name", [$observer->getEvent()->getName()]);
        $this->logger->debug("event",[$myEventData->getSku()]);
    }
}