<?php
namespace Codappix\SearchCore\Connection;

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

/**
 * A single possible option of a facet.
 */
interface FacetOptionInterface
{
    /**
     * Returns the name of this option. Equivalent
     * to value used for filtering.
     */
    public function getName() : string;

    /**
     * If a pre-rendered name is provided, this will be returned.
     * Otherwise it's the same as getName().
     */
    public function getDisplayName() : string;

    /**
     * Returns the number of found results for this option.
     */
    public function getCount() : int;
}
