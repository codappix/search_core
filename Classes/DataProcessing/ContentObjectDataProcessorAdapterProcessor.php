<?php
namespace Codappix\SearchCore\DataProcessing;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Executes an existing TYPO3 DataProcessor on the given data.
 */
class ContentObjectDataProcessorAdapterProcessor implements ProcessorInterface
{
    /**
     * @var TypoScriptService
     */
    protected $typoScriptService;

    public function __construct(TypoScriptService $typoScriptService)
    {
        $this->typoScriptService = $typoScriptService;
    }

    public function processData(array $data, array $configuration) : array
    {
        $dataProcessor = GeneralUtility::makeInstance($configuration['_dataProcessor']);
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        $contentObjectRenderer->data = $data;
        if (isset($configuration['_table'])) {
            $contentObjectRenderer->start($data, $configuration['_table']);
        }

        return $dataProcessor->process(
            $contentObjectRenderer,
            [],
            $this->typoScriptService->convertPlainArrayToTypoScriptArray($configuration),
            $data
        );
    }
}
