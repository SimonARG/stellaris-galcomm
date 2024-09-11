<?php

namespace App\Services\Stellaris;

class StellarisTraitService
{
    public function readGameFile(string $filePath): string
    {
        // Handle reading file contents here
        return file_get_contents($filePath);
    }

    public function parseGameData($fileContent)
    {
        $traits = [];
        $lines = explode("\n", $fileContent);
        $currentTrait = null;
        $inTrait = false;
        $braceCount = 0;
        $inInlineScript = false;
        $currentSection = null;
        $skipSection = false;
    
        foreach ($lines as $index => $line) {
            $line = trim($line);
    
            // Skip comments and empty lines
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
    
            // Start of a new trait
            if (preg_match('/^(trait_ruler_\w+|leader_trait_\w+)\s*=\s*{/', $line, $matches)) {
                $currentTrait = $matches[1];
                $traits[$currentTrait] = [
                    'name' => str_replace(['trait_ruler_', 'leader_trait_'], '', $currentTrait),
                    'cost' => 0,
                    'class' => '',
                    'council' => false,
                    'tier' => 0,
                    'icon' => '',
                    'modifiers' => [],
                    'councilor_modifiers' => [],
                    'requirements' => [],
                    'opposites' => [],
                ];
                $inTrait = true;
                $braceCount = 1;
                continue;
            }
    
            if ($inTrait) {
                // Count braces to determine when we're out of the trait
                $braceCount += substr_count($line, '{') - substr_count($line, '}');
                if ($braceCount == 0) {
                    $inTrait = false;
                    $currentTrait = null;
                    $inInlineScript = false;
                    $currentSection = null;
                    $skipSection = false;
                    continue;
                }
    
                // Handle inline_script section
                if (strpos($line, 'inline_script') !== false) {
                    $inInlineScript = true;
                    continue;
                }
    
                if ($inInlineScript) {
                    if (preg_match('/CLASS\s*=\s*(\w+)/', $line, $matches)) {
                        // Only capture the main class, ignore subclasses
                        $traits[$currentTrait]['class'] = explode('_', $matches[1])[0];
                    } elseif (preg_match('/ICON\s*=\s*"([^"]+)"/', $line, $matches)) {
                        $traits[$currentTrait]['icon'] = $matches[1];
                    } elseif (preg_match('/COUNCIL\s*=\s*(\w+)/', $line, $matches)) {
                        $traits[$currentTrait]['council'] = ($matches[1] === 'yes');
                    } elseif (preg_match('/TIER\s*=\s*(\w+)/', $line, $matches)) {
                        $traits[$currentTrait]['tier'] = $matches[1] === 'none' ? 0 : intval($matches[1]);
                    }
                    if (strpos($line, '}') !== false) {
                        $inInlineScript = false;
                    }
                    continue;
                }
    
                // Ignore selectable_weight and ai_weight sections
                if (strpos($line, 'selectable_weight') !== false || strpos($line, 'ai_weight') !== false) {
                    $skipSection = true;
                    continue;
                }
    
                if ($skipSection) {
                    if (strpos($line, '}') !== false) {
                        $skipSection = false;
                    }
                    continue;
                }
    
                // Parse other trait properties
                if (strpos($line, 'cost') !== false) {
                    if (preg_match('/cost\s*=\s*(\d+)/', $line, $matches)) {
                        $traits[$currentTrait]['cost'] = intval($matches[1]);
                    }
                } elseif (strpos($line, 'modifier') !== false) {
                    $currentSection = strpos($line, 'councilor_modifier') !== false ? 'councilor_modifiers' : 'modifiers';
                } elseif (strpos($line, 'leader_potential_add') !== false) {
                    $currentSection = 'requirements';
                } elseif (strpos($line, 'opposites') !== false) {
                    $currentSection = 'opposites';
                } elseif ($currentSection && strpos($line, '=') !== false) {
                    $line = trim($line, ',');
                    list($key, $value) = array_map('trim', explode('=', $line, 2));
                    if ($currentSection === 'requirements') {
                        $traits[$currentTrait][$currentSection][$key] = ($value === 'yes' || $value === 'no') ? ($value === 'yes') : $value;
                    } elseif ($currentSection === 'modifiers' || $currentSection === 'councilor_modifiers') {
                        $traits[$currentTrait][$currentSection][] = "$key = $value";
                    }
                } elseif ($currentSection === 'opposites') {
                    $value = trim($line, '{ }');
                    if (!empty($value)) {
                        $traits[$currentTrait][$currentSection][] = $value;
                    }
                }
    
                if (strpos($line, '}') !== false && $currentSection) {
                    $currentSection = null;
                }
            }
        }
    
        return $traits;
    }
    

    public function saveToDatabase($parsedData)
    {
        // Handle saving the parsed data into the database
        foreach ($parsedData as $trait => $details) {
            // Save each parsed trait data
        }
    }
}
