<?php

namespace Raindrop\RoutingBundle\Resolver;

use Symfony\Component\Routing\Exception\InvalidParameterException;

class ContentResolver implements ContentResolverInterface {

    protected $entityManager;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function getContent($routeObject) {
        $content = null;

        $routeContent = $routeObject->getRouteContent();

        if (!empty($routeContent)) {
            list($model, $field, $value) = explode(':', $routeContent);
            if (empty($field) or $field === 'id') {
                $content = $this->entityManager->getRepository($model)->find($value);
            } else {

                if (substr($field, strlen($field)-3) === '(s)') {
                    $finderMethod = 'findBy' . ucfirst(substr($field, 0, strlen($field)-3));
                } else {
                    $finderMethod = 'findOneBy' . ucfirst($field);
                }
                $content = $this->entityManager->getRepository($model)->$finderMethod($value);
            }
        }

        return $content;
    }

    public function getRouteContent($content, $field = 'id') {
        if (is_array($content)) {
            return $this->getRouteContentForCollection($content, $field);
        }

        $getter = 'get' . ucfirst($field);
        $routeContentArray = array(get_class($content), $field, $content->$getter());

        // omit 'id' as it is implicit
        if ($field === 'id') {
            $routeContentArray[1] = '';
        }

        return implode(':', $routeContentArray);
    }

    protected function getRouteContentForCollection($collection, $field) {
        if ($field === 'id') {
            throw new InvalidParameterException('Route::setCollection field parameter cannot be an id');
        }

        $getter = 'get' . ucfirst($field);
        $class = get_class($collection[0]);
        $fields = $field . '(s)';
        $value = $collection[0]->$getter();

        $routeContentArray = array($class, $fields, $value);

        return implode(':', $routeContentArray);
    }
}
