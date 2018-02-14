<?php
/**
 * Created by Afroze.S.
 * Date: 14/2/18
 * Time: 11:33 AM
 */

namespace Twentyone\UpdateOnBuy\Observer;


use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MagentoEnv\Entity\ConfigEnv;
use Monolog\Logger;

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
     * @var Logger
     */
    private $logger;

    /**
     * UpdateStockObserver constructor.
     * @param Logger $logger
     * @param ConfigEnv $configEnv
     * @param Product $product
     */
    public function __construct(Logger $logger,
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
        $myEventData = $observer->getData('controller_action_catalog_product_save_entity_after');

        $this->logger->addDebug($myEventData);
    }
}