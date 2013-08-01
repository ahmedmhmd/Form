<?php
namespace Lrotherfield\Component\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class EntityToIntTransformer
 *
 * Transform an entity to  and from it's 'id' field
 *
 * @package Lrotherfield\Bundle\TestBundle\Form\DataTransformer
 * @author Luke Rotherfield <luke@lrotherfield.com>
 */
class EntityToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;
    private $entityRepository;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * If the passed argument is a valid entity return the id, else throw an exception
     * @param mixed $entity
     * @throws TransformationFailedException
     * @return integer|string
     */
    public function transform($entity)
    {
        if (is_null($entity) || !is_object($entity) || !method_exists($entity, 'getId')) {
            throw new TransformationFailedException("Failed to get identifier from passed entity value");
        }

        return $entity->getId();
    }

    /**
     * Attempt to retrieve the entity from the database using the passed argument
     * as the id field
     * @param integer $id
     * @throws TransformationFailedException
     * @return object
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            throw new TransformationFailedException("No id was submitted");
        }

        $entity = $this->om->getRepository($this->entityRepository)->find($id);

        if (null === $entity) {
            throw new TransformationFailedException(sprintf(
                'An entity with id "%s" does not exist!',
                $id
            ));
        }

        return $entity;
    }

    /**
     * Set the repository in shorthand or FQN string
     * @param string $entityRepository
     */
    public function setEntityRepository($entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

}