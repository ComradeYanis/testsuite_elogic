<?php

declare(strict_types=1);

namespace Elogic\CustomerShoppingCart\Block\Onepage;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ExportCartItems
 * @package Elogic\CustomerShoppingCart\Block\Onepage
 */
class ExportCartItems extends Template
{
    const SHOPPING_CART_EXPORT_CONFIG = 'customer/shopping_cart_export/enable_status';
    const EXPORT_BUTTON_LABEL = 'Export cart to CSV';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * ExportCartItems constructor.
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isExportShoppingCartEnable(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::SHOPPING_CART_EXPORT_CONFIG,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getExportCartInCsvUrl(): string
    {
        return $this->getUrl('checkout/checkout/export', ['_secure' => true]);
    }
}
