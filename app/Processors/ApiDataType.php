<?php

namespace App\Processors;

class ApiDataType
{
    public static function get($type)
    {
        $return = [
            'type'   => 'string',
            'format' => 'string',
        ];
        switch ($type) {
            case 'bigint':
            case 'smallint':
            case 'integer':
            case 'boolean':
                $return['type']   = 'integer';
                $return['format'] = 'int32';
                break;
            case 'decimal':
                $return['type']   = 'number';
                $return['format'] = 'float';
                break;
            case 'date':
                $return['format'] = 'date';
                break;
            case 'datetime':
                $return['format'] = 'date-time';
                break;
            default:
                break;
        }

        return $return;
    }
}
