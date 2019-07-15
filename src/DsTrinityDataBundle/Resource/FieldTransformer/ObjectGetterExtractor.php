<?php

namespace DsTrinityDataBundle\Resource\FieldTransformer;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectGetterExtractor implements FieldTransformerInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['method']);
        $resolver->setAllowedTypes('method', ['string']);
        $resolver->setDefaults([
            'method' => 'id'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function transformData(string $dispatchTransformerName, ResourceContainerInterface $resourceContainer)
    {
        if (!$resourceContainer->hasAttribute('type')) {
            return null;
        }

        $data = $resourceContainer->getResource();
        $type = $resourceContainer->getAttribute('type');
        $dataType = $resourceContainer->getAttribute('data_type');

        if (!method_exists($data, $this->options['method'])) {
            return null;
        }

        $value = call_user_func([$data, $this->options['method']]);
        if (!is_string($value)) {
            return null;
        }

        return $value;
    }
}