<?php namespace Pz\Doctrine\Rest\Traits;

use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Hydrator\ArrayHydrator;
use pmill\Doctrine\Hydrator\JsonApiHydrator;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

trait CanHydrate
{
    /**
     * @param string|object       $entity
     * @param EntityManager       $em
     * @param RestRequestContract $request
     *
     * @return object
     * @throws \Exception
     */
    protected function hydrate($entity, EntityManager $em, RestRequestContract $request)
    {
        if ($request->isContentJsonApi()) {
            return (new JsonApiHydrator($em))->hydrate($entity, $request->all()['data']);
        }

        return (new ArrayHydrator($em))->hydrate($entity, $request->all());
    }
}
