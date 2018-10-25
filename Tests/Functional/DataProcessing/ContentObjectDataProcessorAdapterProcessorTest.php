<?php

namespace Codappix\SearchCore\Tests\Functional\DataProcessing;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use Codappix\SearchCore\Compatibility\TypoScriptService;
use Codappix\SearchCore\Compatibility\TypoScriptService76;
use Codappix\SearchCore\DataProcessing\ContentObjectDataProcessorAdapterProcessor;
use Codappix\SearchCore\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Frontend\DataProcessing\SplitProcessor;

class ContentObjectDataProcessorAdapterProcessorTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     */
    public function contentObjectDataProcessorIsExecuted()
    {
        $record = ['content' => 'value1, value2'];
        $configuration = [
            '_dataProcessor' => SplitProcessor::class,
            'delimiter' => ',',
            'fieldName' => 'content',
            'as' => 'new_content',
        ];
        $expectedData = [
            'content' => 'value1, value2',
            'new_content' => ['value1', 'value2'],
        ];

        if ($this->isLegacyVersion()) {
            $typoScriptService = new TypoScriptService76();
        } else {
            $typoScriptService = new TypoScriptService();
        }

        $subject = new ContentObjectDataProcessorAdapterProcessor($typoScriptService);
        $processedData = $subject->processData($record, $configuration);
        $this->assertSame(
            $expectedData,
            $processedData,
            'The processor did not return the expected processed record.'
        );
    }
}
