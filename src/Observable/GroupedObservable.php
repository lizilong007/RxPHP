<?php

namespace Rx\Observable;

use Rx\Observable;
use Rx\ObserverInterface;
use Rx\ObservableInterface;
use Rx\Disposable\CompositeDisposable;
use Rx\DisposableInterface;

class GroupedObservable extends Observable
{
    private $key;
    private $underlyingObservable;

    public function __construct($key, ObservableInterface $underlyingObservable, DisposableInterface $mergedDisposable = null)
    {
        $this->key = $key;

        if (null === $mergedDisposable) {
            $this->underlyingObservable = $underlyingObservable;
        } else {
            $this->underlyingObservable = new AnonymousObservable(
                function ($observer) use ($mergedDisposable, $underlyingObservable) {
                    // todo, typehint $mergedDisposable?
                    return new CompositeDisposable([
                        $mergedDisposable->getDisposable(),
                        $underlyingObservable->subscribe($observer),
                    ]);
                }
            );
        }
    }

    public function getKey()
    {
        return $this->key;
    }

    protected function _subscribe(ObserverInterface $observer): DisposableInterface
    {
        return $this->underlyingObservable->subscribe($observer);
    }
}