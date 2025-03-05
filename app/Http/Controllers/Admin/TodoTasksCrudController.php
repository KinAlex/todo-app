<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TodoTaskStatus;
use App\Http\Requests\TodoTasksRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TodoTasksCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TodoTasksCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\TodoTasks::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/todo-tasks');
        CRUD::setEntityNameStrings('todo tasks', 'todo tasks');

        if (backpack_auth()->user()->is_admin === false) {
            CRUD::addClause('where', 'user_id', '=', backpack_auth()->user()->id);
        }
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        $this->crud->removeColumn('user_id');
        $this->crud->modifyColumn('date', [
            'label' => 'Дата',
            'format' => 'DD.MM.Y - HH:mm',
        ]);
        $this->crud->modifyColumn('name', [
            'label' => 'Заголовок',
        ]);
        $this->crud->modifyColumn('description', [
            'label' => 'Описание',
        ]);
        $this->crud->modifyColumn('status', [
            'label' => 'Статус',
            'type' => 'closure',
            'function' => function($entry) {
                return TodoTaskStatus::{$entry->status}->value;
            },
        ]);
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(TodoTasksRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        $this->crud->modifyField('user_id', [
            'type' => 'hidden',
        ]);
        $this->crud->modifyField('date', [
            'label' => 'Дата',
        ]);
        $this->crud->modifyField('name', [
            'label' => 'Заголовок',
        ]);
        $this->crud->modifyField('description', [
            'label' => 'Описание',
        ]);
        $this->crud->field('status')
            ->label('Статус')
            ->type('select_from_array')
            ->allows_null(false)
            ->options(array_column(TodoTaskStatus::cases(), 'value', 'name'))
        ;
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
