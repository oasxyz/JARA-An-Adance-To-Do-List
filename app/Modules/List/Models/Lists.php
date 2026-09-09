<?php

namespace App\Modules\List\Models;

/**
 * Class Lists
 *
 * Alternative alias for TodoList model to represent the 'lists' entity
 * while circumventing the PHP reserved keyword 'list'.
 */
class Lists extends TodoList
{
    // Inherits all table configuration, attributes, and relationships from TodoList
}

