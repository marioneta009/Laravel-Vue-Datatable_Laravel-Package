<?php

namespace JamesDordoy\LaravelVueDatatable\Classes\Joins;

use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipModelNotSetException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipColumnsNotFoundException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipForeignKeyNotSetException;

class JoinHasOneRelationships
{
    public function __invoke($query, $localModel, $relationships, $relationshipModelFactory)
    {
        if (isset($relationships['belongsTo'])) {
            foreach ($relationships['belongsTo'] as $tableName => $options) {

                if (! isset($options['model'])) {     
                    throw new RelationshipModelNotSetException(
                        "Model not set on relationship: $tableName"
                    );
                }

                if (! isset($options['foreign_key'])) {
                    throw new RelationshipForeignKeyNotSetException(
                        "Foreign Key not set on relationship: $tableName"
                    );
                }

                if (! isset($options['columns'])) {
                    throw new RelationshipColumnsNotFoundException(
                        "Columns array not set on relationship: $tableName"
                    );
                }

                $model = $relationshipModelFactory($options['model'], $tableName);

                //Join the table so it can be orderBy
                $query = $query->leftJoin(
                    $model->getTable(),
                    $localModel->getTable().".".$options['foreign_key'],
                    '=',
                    $model->getTable().".".$model->getKeyName()
                );
            }
        }

        return $query;
    }
}