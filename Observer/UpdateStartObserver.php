<?php
/**
 * Created by Afroze.S.
 * Date: 14/2/18
 * Time: 6:13 PM
 */

namespace Twentyone\UpdateStock\Observer;


use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use MagentoEnv\Entity\ConfigEnv;
use Psr\Log\LoggerInterface;
use Twentyone\UpdateStock\ServiceEntity\SoapEntity;

class UpdateStartObserver  implements ObserverInterface
{
    /**
     * @var ConfigEnv
     */
    private $configEnv;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Product
     */
    private $productModel;
    /**
     * @var Config
     */
    private $eavConfig;
    /**
     * @var SoapEntity
     */
    private $soapEntity;

    /**
     * UpdateStockObserver constructor.
     * @param ConfigEnv $configEnv
     * @param Config $eavConfig
     * @param Product $productModel
     * @param LoggerInterface $logger
     * @param SoapEntity $soapEntity
     */
    public function __construct(ConfigEnv $configEnv,
                                Config $eavConfig,
                                Product $productModel,
                                LoggerInterface $logger,
                                SoapEntity $soapEntity)
    {
        $this->configEnv = $configEnv;
        $this->logger = $logger;
        $this->productModel = $productModel;
        $this->eavConfig = $eavConfig;
        $this->soapEntity = $soapEntity;
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
        $products = $order->getItems();

        $cancelFlag = false;

        foreach ($products as $product) {
            if(strtolower($product->getProductType()) == 'simple') {
                $pr = clone $this->productModel;
                $pr->load($product->getProductId());
                $sizeAttribute = self::getSizeAttributeCode($pr->getAttributeSetId());
                $sizeAttributeValue = $pr->getData($sizeAttribute);
                $sizeAttributeValueText = self::getAttributeValueById($this->eavConfig, $sizeAttribute, $sizeAttributeValue);

                $atAvailability = $this->soapEntity->checkAvailabilityInAtelier($pr->getData('id_atelier'), $sizeAttributeValueText);
                if ($atAvailability < 1 && $atAvailability == null) {
                    $cancelFlag = true;
                }
            }
        }

        if ($cancelFlag) {
            //order cancelled
            $order->cancel();
        }
        $this->logger->debug("order status: ".$order->getStatus());
    }

    /**
     * @param string $attributeSetId
     * @return null|string
     */
    public static function getSizeAttributeCode($attributeSetId) {

        $attributeCode = null;
        switch ($attributeSetId) {
            case 4:
                $attributeCode = "size_clothes";
                break;
            case 21:
                $attributeCode = "jeans_size";
                break;
            case 16:
                $attributeCode = "jeans_size";
                break;
            case 15:
                $attributeCode = "size_clothes_men_top";
                break;
            case 14:
                $attributeCode = "size_clothes";
                break;
            case 12:
                $attributeCode = "size_letters";
                break;
            case 11:
                $attributeCode = "size_shirts";
                break;
            case 10:
                $attributeCode = "size_shoes_men";
                break;
            case 9:
                $attributeCode = "size_shoes_women";
                break;
            case 22:
                $attributeCode = "size_belt";
                break;
        }

        return $attributeCode;
    }

    /**
     * @param Config $eavConfig
     * @param string $attributeCode
     * @param string $attributeValue
     * @return null|string|int
     */
    public static function getAttributeValueById(Config $eavConfig, $attributeCode, $attributeValue) {

        $option = null;
        try {
            $attribute = $eavConfig->getAttribute('catalog_product', $attributeCode);
            $option = $attribute->getSource()->getOptionText($attributeValue);
        } catch (LocalizedException $e) {
            print('Error: '.$e->getMessage());
        }
        return $option;
    }
}