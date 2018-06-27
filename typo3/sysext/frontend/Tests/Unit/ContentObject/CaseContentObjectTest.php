<?php
declare(strict_types = 1);

namespace TYPO3\CMS\Frontend\Tests\Unit\ContentObject;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Frontend\ContentObject\CaseContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\TextContentObject;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class CaseContentObjectTest extends UnitTestCase
{
    /**
     * @var bool Reset singletons created by subject
     */
    protected $resetSingletonInstances = true;

    /**
     * @var CaseContentObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = $this->getMockBuilder(TypoScriptFrontendController::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $tsfe->tmpl = $this->getMockBuilder(TemplateService::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $tsfe->config = [];
        $tsfe->page = [];
        $tsfe->sys_page = $this->getMockBuilder(PageRepository::class)
            ->setMethods(['getRawRecord'])
            ->getMock();
        $GLOBALS['TSFE'] = $tsfe;

        $contentObjectRenderer = new ContentObjectRenderer();
        $contentObjectRenderer->setContentObjectClassMap([
            'CASE' => CaseContentObject::class,
            'TEXT' => TextContentObject::class,
        ]);
        $this->subject = new CaseContentObject($contentObjectRenderer);
    }

    /**
     * @test
     */
    public function renderReturnsEmptyStringIfNoKeyMatchesAndIfNoDefaultObjectIsSet(): void
    {
        $conf = [
            'key' => 'not existing'
        ];
        $this->assertSame('', $this->subject->render($conf));
    }

    /**
     * @test
     */
    public function renderReturnsContentFromDefaultObjectIfKeyDoesNotExist(): void
    {
        $conf = [
            'key' => 'not existing',
            'default' => 'TEXT',
            'default.' => [
                'value' => 'expected value'
            ],
        ];
        $this->assertSame('expected value', $this->subject->render($conf));
    }
}
