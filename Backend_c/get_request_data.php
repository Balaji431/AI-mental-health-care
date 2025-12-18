<?php
/**
 * Read request input and return associative array.
 * Priority:
 *  1) JSON body (if any)
 *  2) $_POST (form-data or x-www-form-urlencoded)
 *  3) $_GET (only if method is GET) - for quick testing only
 *
 * Returns array on success, or null if no usable input found.
 */
function get_request_data() : ?array {
    // 1) Try JSON body
    $raw = file_get_contents('php://input');
    if ($raw !== null && trim($raw) !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }
        // If raw exists but JSON invalid, we return null so caller can decide to send a 400 with details.
        return null;
    }

    // 2) Fallback to $_POST if present (form-data or x-www-form-urlencoded)
    if (!empty($_POST)) {
        // Convert $_POST (which may be an array-like object) into a plain array
        return array_map(function($v){ return is_string($v) ? trim($v) : $v; }, $_POST);
    }

    // 3) If GET request, use query params (only for local testing)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
        return array_map(function($v){ return is_string($v) ? trim($v) : $v; }, $_GET);
    }

    // Nothing useful found
    return null;
}
