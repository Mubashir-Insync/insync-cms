<?php
/*
 * Copyright (C) 2022 Xibo Signage Ltd
 *
 * Xibo - Digital Signage - http://www.xibo.org.uk
 *
 * This file is part of Xibo.
 *
 * Xibo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Xibo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Xibo.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Xibo\Entity;

/**
 * A trait for common fuctionality with regards to properties on modules/module templates
 */
trait ModulePropertyTrait
{
    /**
     * @param \Xibo\Entity\Widget $widget
     * @param bool $includeDefaults
     * @return $this
     */
    public function decorateProperties(Widget $widget, bool $includeDefaults = false)
    {
        foreach ($this->properties as $property) {
            $property->value = $widget->getOptionValue($property->id, null);

            // Should we include defaults?
            if ($includeDefaults && $property->value === null) {
                $property->value = $property->default;
            }
        }
        return $this;
    }

    /**
     * @param bool $decorateLibraryRefs
     * @return array
     */
    public function getPropertyValues(bool $decorateLibraryRefs = true): array
    {
        $properties = [];
        foreach ($this->properties as $property) {
            $value = $property->value;
            // Does this property have library references?
            if ($decorateLibraryRefs && $property->allowLibraryRefs) {
                // Parse them out and replace for our special syntax.
                $matches = [];
                preg_match_all('/\[(.*?)\]/', $value, $matches);
                foreach ($matches[1] as $match) {
                    if (is_numeric($match)) {
                        $value = str_replace('[' . $match . ']', '[[mediaId=' . $match . ']]', $value);
                    }
                }
            }
            $properties[$property->id] = $value;
        }
        return $properties;
    }

    /**
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     */
    public function validateProperties(): void
    {
        // Go through all of our required properties, and validate that they are as they should be.
        foreach ($this->properties as $property) {
            $property->validate();
        }
    }
}