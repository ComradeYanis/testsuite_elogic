<?php
declare(strict_types=1);

namespace Elogic\CustomerShoppingCart\Test\Unit\Controller\Checkout;

use Elogic\CustomerShoppingCart\Controller\Checkout\Export;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\DataObject;
use Magento\Framework\File\Csv;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class ExportTest
 * @package Elogic\CustomerShoppingCart\Test\Unit\Controller\Checkout
 */
class ExportTest extends TestCase
{

    /**
     * @var Export
     */
    protected $controller;
    /**
     * @var MockObject
     */
    protected $quote;
    /**
     * @var MockObject
     */
    protected $checkoutSession;
    /**
     * @var MockObject
     */
    protected $fileFactory;
    /**
     * @var MockObject
     */
    protected $csvProcessor;
    /**
     * @var MockObject
     */
    protected $directoryList;
    /**
     * @var MockObject
     */
    protected $logger;


    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);
        $this->quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->quote->method('getItems')
            ->willReturn([new DataObject(['name' => '1', 'price' => 1.21, 'qty' => 1])]);
        $this->checkoutSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkoutSession->method('getQuote')->willReturn($this->quote);
        $this->fileFactory = $this->getMockBuilder(FileFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->csvProcessor = $this->getMockBuilder(Csv::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->csvProcessor->method('setDelimiter')->willReturnSelf();
        $this->csvProcessor->method('setEnclosure')->willReturnSelf();
        $this->csvProcessor->method('appendData')->willReturnSelf();
        $this->directoryList = $this->getMockBuilder(DirectoryList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->directoryList->method('getPath')->willReturn('path');
        $this->logger = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->controller = $objectManagerHelper->getObject(
            Export::class,
            [
                'checkoutSession' => $this->checkoutSession,
                'fileFactory' => $this->fileFactory,
                'csvProcessor' => $this->csvProcessor,
                'directoryList' => $this->directoryList,
                'logger' => $this->logger,
            ]
        );
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function testExecute()
    {
        $this->controller->execute();
    }
}
