<?php

return [
    // Debug code that will be returned when using the debug method
    'debugging_response_code' => 500,
    // Size of the debug code within the error
    'debug_code_size' => 6,
    'errors' => [
        // Determines if the errors should be logged
        'log' => false,
        // Default response code when an error occurs
        'code' => 500,
        // The model used for logging
        'model' => null,
        // Columns used to store the error information, if any are set to null, they will be placed within the parameters
        'columns' => [
            'message' => 'message',
            'line' => 'line',
            'file' => 'file',
            'code' => 'code',
            // If this is set to null, no additional information will be stored
            'parameters' => 'parameters',
        ],
        // Code that will output if there is a SQL error
        'sql_error_code' => 500,
    ],
];