<?php
namespace Library;

trait EntityHelper
{
    public function __call($method, $args)
    {
        if (preg_match('~^(get|set)(\w+)$~i', $method, $match)) {
            $action = strtolower($match[1]);
            $property = lcfirst($match[2]);

            if (isset($this->$property)) {
                if ($action == 'set') {
                    $this->$property = $args[0];
                    return $this;
                }

                return $this->$property;
            }
        }

        throw new \Exception('Call to undefined method '.get_class($this).'::'.$method);
    }

    public function fromArray($array)
    {
        if (isset($array['id'])) {
            unset($array['id']);
        }

        foreach ($this->toArray() as $key => $value) {
            if (isset($array[$key])) {
                $this->$key = $array[$key];
            }
        }
    }

    public function toArray()
    {
        $vars = get_object_vars($this);

        return array_filter($vars, function($item) {
            return !is_object($item);
        });
    }

    public static function collectionToArray($collection)
    {
        return array_map(function($item) {
            return $item->toArray();
        }, $collection);
    }
}
