<?php

declare(strict_types=1);

namespace SwooleTW\Hyperf\Auth\Providers;

use Closure;
use Hyperf\Contract\Arrayable;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Stringable\Str;
use SwooleTW\Hyperf\Auth\Contracts\Authenticatable;
use SwooleTW\Hyperf\Auth\Contracts\UserProvider;
use SwooleTW\Hyperf\Hashing\Contracts\Hasher as HashContract;

class EloquentUserProvider implements UserProvider
{
    public function __construct(
        protected HashContract $hasher,
        protected string $model
    ) {}

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \SwooleTW\Hyperf\Auth\Contracts\Authenticatable|null
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
                    ->where($model->getAuthIdentifierName(), $identifier)
                    ->first();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \SwooleTW\Hyperf\Auth\Contracts\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            Str::contains($this->firstCredentialKey($credentials), 'password'))) {
            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } elseif ($value instanceof Closure) {
                $value($query);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Get the first key from the credential array.
     *
     * @param  array  $credentials
     * @return string|null
     */
    protected function firstCredentialKey(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            return $key;
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \SwooleTW\Hyperf\Auth\Contracts\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Get a new query builder for the model instance.
     *
     * @param  \Hyperf\Database\Model\Model|null  $model
     * @return \Hyperf\Database\Model\Builder
     */
    protected function newModelQuery(?Model $model = null): Builder
    {
        return is_null($model)
                ? $this->createModel()->newQuery()
                : $model->newQuery();
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Hyperf\Database\Model\Model
     */
    public function createModel(): Model
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Gets the hasher implementation.
     *
     * @return \SwooleTW\Hyperf\Hashing\Contracts\Hasher
     */
    public function getHasher(): HashContract
    {
        return $this->hasher;
    }

    /**
     * Sets the hasher implementation.
     *
     * @param  \SwooleTW\Hyperf\Hashing\Contracts\Hasher  $hasher
     * @return $this
     */
    public function setHasher(HashContract $hasher): static
    {
        $this->hasher = $hasher;

        return $this;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  string  $model
     * @return $this
     */
    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }
}