<?php

namespace RhMachine\Repository;

use CCMBenchmark\Ting\Exception;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use Mouf\Security\UserService\UserDaoInterface;
use Mouf\Security\UserService\UserInterface;
use RhMachine\Entity\Request;

class RequestRepository extends Repository implements UserDaoInterface
{

    /**
     * @param SerializerFactoryInterface $serializerFactory
     *
     * @return Metadata
     * @throws Exception
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Request::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase(DB_NAME);
        $metadata->setTable('request');

        $metadata
            ->addField([
                'primary'       => true,
                'autoincrement' => true,
                'fieldName'     => 'id',
                'columnName'    => 'id',
                'type'          => 'int'
            ])
            ->addField([
                'fieldName'  => 'contact',
                'columnName' => 'contact',
                'type'       => 'string'
            ])
            ->addField([
                'fieldName'  => 'source',
                'columnName' => 'source',
                'type'       => 'string'
            ])
            ->addField([
                'fieldName'  => 'status',
                'columnName' => 'status',
                'type'       => 'string'
            ])
            ->addField([
                'fieldName'  => 'modificationDate',
                'columnName' => 'modification_date',
                'type'       => 'datetime'
            ])
            ->addField([
                'fieldName'  => 'creationDate',
                'columnName' => 'creation_date',
                'type'       => 'datetime'
            ]);

        return $metadata;
    }

    /**
     * Returns a user from its login and its password, or null if the login or credentials are false.
     *
     * @param string $login
     * @param string $password
     * @return UserInterface
     */
    public function getUserByCredentials($login, $password) {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = $this->getQuery('SELECT id, login, password FROM user WHERE login = :login AND password = :password');
        $query->setParams([
            'login' => $login,
            'password' => $hashPassword
        ]);
        $userCollection = $query->query($this->getCollection(new HydratorSingleObject()));
        if($userCollection->count() > 1) {
            //TODO throw exception
        }
        return $userCollection->first();
    }

    /**
     * Returns a user from its token.
     *
     * @param string $token
     * @return UserInterface
     */
    public function getUserByToken($token){
        $query = $this->getQuery('SELECT id, login, password FROM user WHERE token = :token');
        $query->setParams([
            'token' => $token
        ]);
        $userCollection = $query->query($this->getCollection(new HydratorSingleObject()));
        if($userCollection->count() > 1) {
            //TODO throw exception
        }
        return $userCollection->first();
    }

    /**
     * Discards a token.
     *
     * @param string $token
     */
    public function discardToken($token) {
        //TODO
    }

    /**
     * Returns a user from its ID
     *
     * @param string $id
     * @return UserInterface
     */
    public function getUserById($id) {
        return $this->get(['id' => $id]);
    }

    /**
     * Returns a user from its login
     *
     * @param string $login
     * @return UserInterface
     */
    public function getUserByLogin($login){
        return $this->get(['login' => $login]);
    }
}