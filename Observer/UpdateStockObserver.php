<?php
/**
 * Created by Afroze.S.
 * Date: 14/2/18
 * Time: 11:33 AM
 */

namespace Twentyone\UpdateStock\Observer;


use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use MagentoEnv\Entity\ConfigEnv;
use Psr\Log\LoggerInterface;
use Twentyone\UpdateStock\ServiceEntity\SoapEntity;

class UpdateStockObserver implements ObserverInterface
{
    /**
     * @var Product
     */
    private $productModel;
    /**
     * @var ConfigEnv
     */
    private $configEnv;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SoapEntity
     */
    private $soapEntity;
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * UpdateStockObserver constructor.
     * @param ConfigEnv $configEnv
     * @param Config $eavConfig
     * @param LoggerInterface $logger
     * @param SoapEntity $soapEntity
     * @param Product $productModel
     */
    public function __construct(ConfigEnv $configEnv,
                                Config $eavConfig,
                                LoggerInterface $logger,
                                SoapEntity $soapEntity,
                                Product $productModel) {
        $this->productModel = $productModel;
        $this->configEnv = $configEnv;
        $this->logger = $logger;
        $this->soapEntity = $soapEntity;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->logger->debug($observer->getEvent()->getName());
        /** @var Order $order */
        $order = $observer->getData('order');
        $client = $order->getCustomer();
        $this->logger->debug($observer->getEvent()->getName());
        if ($order->getCustomerIsGuest()) {
            $this->logger->debug(json_encode($this->soapEntity->updateClient($order->getCustomerEmail(), $order->getShippingAddress()->getFirstname(),  $order->getShippingAddress()->getLastname())));
        }
        $this->logger->debug("test");
        $this->logger->debug("guest".$order->getCustomerIsGuest());
        $this->logger->debug("data: ".$order->getShippingAddress()->getFirstname());
        $this->logger->debug("data: ".$order->getShippingAddress()->getLastname());
        $this->logger->debug("data: ".$order->getCustomerLastname());
        $this->logger->debug("data: ".$order->getCustomerEmail());
        $this->logger->debug("data: ".$order->getShippingAmount());
        $this->logger->debug("data: ".$order->getTotalPaid());
        $this->logger->debug("data: ".$order->getDiscountAmount());
        $this->logger->debug("data: ".$order->getPaymentAuthorizationAmount());
        foreach ($order->getItems() as $product) {
            if (strtolower($product->getProductType()) == 'simple') {
                $pr = clone $this->productModel;
                $pr->load($product->getProductId());
                $sizeAttribute = UpdateStartObserver::getSizeAttributeCode($pr->getAttributeSetId());
                $idAtelier = $pr->getData('id_atelier');
                $sizeAttributeValue = $pr->getData($sizeAttribute);
                $sizeAttributeValueText = UpdateStartObserver::getAttributeValueById($this->eavConfig, $sizeAttribute, $sizeAttributeValue);

                $this->logger->debug("data: ".$order->getExtOrderId());
                $this->logger->debug("data: ".$order->getRealOrderId());
                $this->logger->debug("data: ".$order->getId());
                $this->logger->debug("data: ".$order->getShippingAddress()->getStreetLine(1));
                $this->logger->debug("data: ".$order->getShippingAddress()->getStreetLine(2));
                $this->logger->debug("data: ".$order->getShippingAddress()->getCity());
                $this->logger->debug("data: ".$order->getShippingAddress()->getRegion());
                $this->logger->debug("data: ".$order->getShippingAddress()->getPostcode());
                //$this->logger->debug("data: ".$order->getShippingAddress()->getRegionCode());
                $this->logger->debug("data: ".$order->getShippingAddress()->getName());
                $this->logger->debug("data: ".$product->getPrice());
                $this->logger->debug("data: ".$product->getQtyOrdered());
                $this->logger->debug("data: ".$product->getQtyOrdered());
                $this->logger->debug("data: ".$product->getTaxAmount());
                $this->logger->debug("data: ".$product->getRowTotal());
                $this->logger->debug(json_encode($this->soapEntity->updateOrder($order->getCustomerEmail(), $idAtelier, $sizeAttributeValueText, $order->getRealOrderId(), $order->getShippingAddress()->getStreetLine(1), $order->getShippingAddress()->getPostcode()." ,".$order->getShippingAddress()->getCity()." ".$order->getShippingAddress()->getRegion(), $order->getShippingAddress()->getCountryId(), $product->getRowTotal(), $product->getQtyOrdered())));
                $this->logger->debug($this->soapEntity->communicateShippingFare($order->getCustomerEmail(), $order->getRealOrderId(), $order->getShippingAmount()));
                $this->logger->debug($this->soapEntity->communicateOrderStatus($order->getCustomerEmail(), $order->getRealOrderId(), $order->getState()));
                //after update do shipping and payment calls with service entity
            }
        }
        //$this->logger->debug("payment:".$myEventData->convertToJson());

    }
}
