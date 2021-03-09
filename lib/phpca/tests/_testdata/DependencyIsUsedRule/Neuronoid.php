<?php

class Neuronoid
{
    public function Synapse(): void
    {
        $factory = new Component\Offense\ParticleFactory();
        $synapseCore = $factory->create();
        $synapseCore->initialize();
    }
}
