<?php

namespace VanDmade\Respondify;

use Auth;

class Respondify
{

    /**************************************************************************\
     * Helps generate the response
     * 
     * @param array $parameters List of details to respond with
     * 
     * @return array The default success response for Respondify class
    \**************************************************************************/
    public static function success($parameters)
    {
        // Makes sure the information sent in is an array, if it isn't it forces the data type
        $parameters = !is_array($parameters) ? [$parameters] : $parameters;
        return response()->json($parameters, 200);
    }

    /**************************************************************************\
     * Generates and logs an error that occurs
     * 
     * @param array $error List of variables used to generate / parse the data
     * @param array $parameters List of additional information to replace within the response with
     * 
     * @return array The default error response for the Respondify class
    \**************************************************************************/
    public static function error($error, $parameters = [])
    {
        $debug = env('APP_DEBUG', false);
        $debugCode = uniqid();
        // Sets the message in a variable to remove server, SQL, and code errors that will make no sense to the user
        $message = isset($parameters['message']) ? $parameters['message'] : $error->getMessage();
        $responseCode = isset($parameters['code']) ? $parameters['code'] : $error->getCode();
        if ($responseCode == 0) {
            $responseCode = 500;
        }
        if (!$debug) {
            // Lists out the cleaned error messages to prevent a user from recieving a message that doesn't make sense
            if (strpos($message, 'SQLERROR') !== false || true) {
                // TODO :: Write default message for an SQL error
                $message = __('respondify.sql_error');
                $responseCode = config('respondify.errors.sql_error_code', 500);
            }
        }
        $responseCode = config('respondify.errors.code', 500);
        // Logs the errors of the site
        if (config('respondify.errors.log', false) && !is_null($model = config('respondify.errors.model', null))) {
            $model = new $model();
            $model->user_id = Auth::check() ? Auth::user()->id : null;
            $args = [];
            foreach (['message', 'line', 'file', 'code'] as $i => $key) {
                $value = null;
                switch ($key) {
                    case 'message': $value = $error->getMessage(); break;
                    case 'line': $value = $error->getLine(); break;
                    case 'file': $value = $error->getFile(); break;
                    case 'code': $value = $error->getCode(); break;
                }
                if (!is_null($column = config('respondify.errors.columns.'.$key))) {
                    $model->$column = $value;
                } else {
                    $args[$key] = $value;
                }
            }
            if (!is_null(config('respondify.errors.columns.parameters', null))) {
                $args['response'] = [
                    'message' => $message != $error->getMessage() ? $message : null,
                    'code' => $responseCode != $error->getCode() ? $responseCode : null,
                ];
                $model->parameters = $args;
            }
            $model->save();
        }
        return response()->json(
            array_merge([
                'success' => false,
                'debug_code' => $debugCode,
                'message' => $message,
            ],
            // Appends debugging information that should only be displayed to certain users
            $debug ? [
                'line' => $error->getLine() ?? null,
                'file' => $error->getFile() ?? null,
            ] : []
        ), $responseCode);
    }

    /**************************************************************************\
     * Outputs the entire array that was sent in order debugging purposes
     * 
     * @param array $parameters List of details to respond with
     * 
     * @return array Array of information to view while debugging
    \**************************************************************************/
    public static function debug($parameters)
    {
        // Makes sure the information sent in is an array, if it isn't it forces the data type
        $parameters = !is_array($parameters) ? [$parameters] : $parameters;
        return response()->json($parameters, config('respondify.debug_code', 500));
    }

}