<?php

declare(strict_types=1);

namespace Elogic\CustomerShoppingCart\Controller\Checkout;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use Psr\Log\LoggerInterface;

/**
 * Class Export
 * @package Elogic\CustomerShoppingCart\Controller\Checkout
 */
class Export implements ActionInterface
{
    const FILE_NAME = 'customer_cart_items.csv';

    /**
     * @var Session
     */
    protected Session $checkoutSession;
    /**
     * @var FileFactory
     */
    protected FileFactory $fileFactory;
    /**
     * @var Csv
     */
    protected Csv $csvProcessor;
    /**
     * @var DirectoryList
     */
    protected DirectoryList $directoryList;
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Export constructor.
     * @param Session $checkoutSession
     * @param FileFactory $fileFactory
     * @param Csv $csvProcessor
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $checkoutSession,
        FileFactory $fileFactory,
        Csv $csvProcessor,
        DirectoryList $directoryList,
        LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->logger = $logger;
    }

    /**
     * @return void
     * @throws FileSystemException
     */
    public function execute()
    {
        $filePath = $this->directoryList->getPath(DirectoryList::VAR_DIR) . DIRECTORY_SEPARATOR . self::FILE_NAME;

        try {
            $productsData = $this->prepareProductsData();
            $this->csvProcessor
                ->setDelimiter(';')
                ->setEnclosure('"')
                ->appendData($filePath, $productsData);

            $this->fileFactory->create(
                self::FILE_NAME,
                [
                    'type' => "filename",
                    'value' => self::FILE_NAME,
                    'rm' => true
                ],
                DirectoryList::VAR_DIR
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @return array
     */
    private function prepareProductsData(): array
    {
        $result[] = [
            'Title',
            'Price',
            'Qty',
            'Subtotal'
        ];

        try {
            $products = $this->checkoutSession->getQuote()->getItems();

            foreach ($products as $product) {
                $result[] = [
                    $product->getName(),
                    $product->getPrice(),
                    $product->getQty(),
                    $product->getPrice() * $product->getQty()
                ];
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $result;
    }
}
