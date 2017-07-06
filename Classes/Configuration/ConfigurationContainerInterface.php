<?php
namespace Leonmrni\SearchCore\Configuration;

/*
 * Copyright (C) 2016  Daniel Siepmann <coding@daniel-siepmann.de>
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

use TYPO3\CMS\Core\SingletonInterface as Singleton;

/**
 * Container of all configurations for extension.
 * Always inject this to have a single place for configuration and parsing only once.
 */
interface ConfigurationContainerInterface extends Singleton
{
    /**
     * Returns the option defined by section and key.
     * May throw an exception if it's not set or is null.
     *
     * @param string $path In dot notation. E.g. indexer.tca.allowedTables
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function get($path);

    /**
     * Same as get but will not throw an exception but return null.
     *
     * @param string $path In dot notation.
     * @return mixed|null
     */
    public function getIfExists($path);
}
