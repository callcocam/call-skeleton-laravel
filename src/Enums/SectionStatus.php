<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\Plannerate\Enums;

enum SectionStatus: string
{
    case Draft = 'draft';
    case Published = 'published'; 
    
    /**
     * Get all enum values as array
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    /**
     * Get all enum labels as array
     *
     * @return array
     */
    public static function getLabels(): array
    {
        return [
            self::Draft->value => 'Rascunho',
            self::Published->value => 'Publicado', 
        ];
    }
    
    /**
     * Get label for current value
     *
     * @return string
     */
    public function getLabel(): string
    {
        $labels = self::getLabels();
        return $labels[$this->value] ?? 'Desconhecido';
    }
}
