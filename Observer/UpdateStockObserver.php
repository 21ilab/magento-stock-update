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

class UpdateStockObserver implements ObserverInterface
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
     * UpdateStockObserver constructor.
     * @param ConfigEnv $configEnv
     * @param Product $product
     */
    public function __construct(ConfigEnv $configEnv,
                                Product $product)
    {
        $this->product = $product;
        $this->configEnv = $configEnv;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $myEventData = $observer->getData('buy_request');

    }
}