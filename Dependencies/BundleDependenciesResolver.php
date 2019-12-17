<?php


namespace Mmoreram\BaseBundle\Dependencies;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

if (trait_exists('Mmoreram\SymfonyBundleDependencies\BundleDependenciesResolver')) {
    trait BundleDependenciesResolver {
        use \Mmoreram\SymfonyBundleDependencies\BundleDependenciesResolver;
    }
} else {
    trait BundleDependenciesResolver {

        /**
         * Get bundle instances given the namespace stack.
         *
         * @param KernelInterface $kernel
         * @param array           $bundles
         *
         * @return BundleInterface[]
         */
        protected function getBundleInstances(
            KernelInterface $kernel,
            array $bundles
        ): array {

            $bundles = array_map(function($bundle) {
                return is_string($bundle)
                    ? new $bundle($this)
                    : $bundle;
            }, $bundles);

            array_map('get_class', $bundles);
            return $bundles;
        }
    }
}
