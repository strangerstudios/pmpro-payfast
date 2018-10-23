# PMPro PayFast Add On

## Dev Info

### pmpro-payfast.php phpcs after initial phpcbf for spacing

--------------------------------------------------------------------------------
FOUND 8 ERRORS AND 1 WARNING AFFECTING 6 LINES
--------------------------------------------------------------------------------
  2 | ERROR   | [ ] You must use "/**" style comments for a file comment
  2 | ERROR   | [ ] Empty line required before block comment
 13 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks,
    |         |     or question marks
 14 | ERROR   | [ ] You must use "/**" style comments for a function comment
 16 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks,
    |         |     or question marks
 47 | ERROR   | [ ] All output should be run through an escaping function (see
    |         |     the Security sections in the WordPress Developer
    |         |     Handbooks), found '__'.
 47 | ERROR   | [ ] A gettext call containing placeholders was found, but was
    |         |     not accompanied by a "translators:" comment on the line
    |         |     above to clarify the meaning of the placeholders.
 47 | ERROR   | [ ] All output should be run through an escaping function (see
    |         |     the Security sections in the WordPress Developer
    |         |     Handbooks), found 'get_admin_url'.
 83 | WARNING | [x] Equals sign not aligned with surrounding assignments;
    |         |     expected 5 spaces but found 1 space
--------------------------------------------------------------------------------
PHPCBF CAN FIX THE 1 MARKED SNIFF VIOLATIONS AUTOMATICALLY
--------------------------------------------------------------------------------


MBPro-15:services apple$ phpcs --standard=WordPress payfast_itn_handler.php

FILE: ...ple/Local Sites/pmp-apache/app/public/wp-content/plugins/pmpro-payfast/services/payfast_itn_handler.php
-------------------------------------------------------------------------------------------------------------
FOUND 339 ERRORS AND 120 WARNINGS AFFECTING 302 LINES
-------------------------------------------------------------------------------------------------------------
   1 | ERROR   | [ ] Filenames should be all lowercase with hyphens as word separators. Expected
     |         |     payfast-itn-handler.php, but found payfast_itn_handler.php.
   8 | ERROR   | [ ] There must be exactly one blank line after the file comment
   8 | ERROR   | [ ] Missing @package tag in file comment
   9 | WARNING | [ ] Found precision alignment of 1 spaces.
   9 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  14 | WARNING | [x] "require_once" is a statement not a function; no parentheses are required
  17 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  19 | WARNING | [ ] error_log() found. Debug code should not normally be used in production.
  29 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  30 | ERROR   | [ ] Variable "pfFeatures" is not in valid snake_case format
  32 | WARNING | [ ] Not using strict comparison for in_array; supply true for third argument.
  34 | ERROR   | [ ] Variable "pfVersion" is not in valid snake_case format
  34 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 3 spaces but found 1
     |         |     space
  34 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
  35 | ERROR   | [ ] Variable "pfFeatures" is not in valid snake_case format
  35 | ERROR   | [ ] Variable "pfVersion" is not in valid snake_case format
  37 | ERROR   | [ ] Variable "pfFeatures" is not in valid snake_case format
  39 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  40 | ERROR   | [ ] Variable "pfFeatures" is not in valid snake_case format
  41 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  45 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  62 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  73 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  76 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  77 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
  77 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 7 spaces but found 1
     |         |     space
  78 | ERROR   | [ ] Variable "pfErrMsg" is not in valid snake_case format
  78 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 6 spaces but found 1
     |         |     space
  79 | ERROR   | [ ] Variable "pfDone" is not in valid snake_case format
  79 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 8 spaces but found 1
     |         |     space
  80 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
  80 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 8 spaces but found 1
     |         |     space
  81 | ERROR   | [ ] Variable "pfHost" is not in valid snake_case format
  81 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 8 spaces but found 1
     |         |     space
  81 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
  81 | ERROR   | [ ] Use Yoda Condition checks, you must.
  82 | ERROR   | [ ] Variable "pfOrderId" is not in valid snake_case format
  82 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 5 spaces but found 1
     |         |     space
  83 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
  85 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  86 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
  86 | ERROR   | [ ] Variable "pfDone" is not in valid snake_case format
  91 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  92 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
  92 | ERROR   | [ ] Variable "pfDone" is not in valid snake_case format
  94 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
  95 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
  96 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
  99 | WARNING | [ ] print_r() found. Debug code should not normally be used in production.
  99 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 100 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 100 | ERROR   | [ ] Use Yoda Condition checks, you must.
 101 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 101 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 102 | ERROR   | [ ] Variable "pfErrMsg" is not in valid snake_case format
 105 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 106 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 106 | ERROR   | [ ] Variable "pfDone" is not in valid snake_case format
 108 | ERROR   | [ ] Variable "passPhrase" is not in valid snake_case format
 108 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 3 spaces but found 1
     |         |     space
 109 | ERROR   | [ ] Variable "pfPassPhrase" is not in valid snake_case format
 109 | ERROR   | [ ] Variable "passPhrase" is not in valid snake_case format
 109 | ERROR   | [ ] Variable "passPhrase" is not in valid snake_case format
 110 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 111 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 111 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 111 | ERROR   | [ ] Variable "pfPassPhrase" is not in valid snake_case format
 112 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 112 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 113 | ERROR   | [ ] Variable "pfErrMsg" is not in valid snake_case format
 116 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 117 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 117 | ERROR   | [ ] Variable "pfDone" is not in valid snake_case format
 119 | ERROR   | [ ] Detected usage of a non-validated input variable: $_SERVER
 119 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 119 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_SERVER
 120 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 120 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 121 | ERROR   | [ ] Variable "pfErrMsg" is not in valid snake_case format
 124 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 125 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 127 | ERROR   | [ ] Variable "pfValid" is not in valid snake_case format
 127 | ERROR   | [ ] Variable "pfHost" is not in valid snake_case format
 127 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 128 | ERROR   | [ ] Variable "pfValid" is not in valid snake_case format
 129 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 129 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 130 | ERROR   | [ ] Variable "pfErrMsg" is not in valid snake_case format
 135 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 135 | ERROR   | [ ] Variable "pfDone" is not in valid snake_case format
 135 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 135 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 135 | ERROR   | [ ] Use Yoda Condition checks, you must.
 137 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 137 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 138 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 139 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 139 | ERROR   | [ ] Variable "checkTotal" is not in valid snake_case format
 140 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 140 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 141 | ERROR   | [ ] Variable "pfErrMsg" is not in valid snake_case format
 146 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 147 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 147 | ERROR   | [ ] Variable "pfDone" is not in valid snake_case format
 148 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 148 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 148 | ERROR   | [ ] Use Yoda Condition checks, you must.
 148 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 149 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 4 spaces but found 1
     |         |     space
 149 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 150 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 151 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 152 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 154 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 155 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 157 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 158 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 15 spaces but found 1
     |         |     space
 158 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 159 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 162 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 163 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 164 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 173 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 175 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 176 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 177 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 179 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 186 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 186 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 186 | ERROR   | [ ] Use Yoda Condition checks, you must.
 187 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 189 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 189 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 190 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 193 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 199 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 199 | ERROR   | [ ] Use Yoda Condition checks, you must.
 200 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 202 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 204 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 205 | ERROR   | [ ] Use Yoda Condition checks, you must.
 208 | WARNING | [ ] This comment is 58% valid code; is this commented out code?
 208 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 211 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 212 | WARNING | [ ] Usage of a direct database call is discouraged.
 212 | WARNING | [ ] Direct database call without caching detected. Consider using wp_cache_get() /
     |         |     wp_cache_set() or wp_cache_delete().
 212 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found $query
 213 | ERROR   | [ ] Variable "sqlQuery" is not in valid snake_case format
 214 | WARNING | [ ] Usage of a direct database call is discouraged.
 214 | WARNING | [ ] Direct database call without caching detected. Consider using wp_cache_get() /
     |         |     wp_cache_set() or wp_cache_delete().
 214 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found $sqlQuery
 214 | ERROR   | [ ] Variable "sqlQuery" is not in valid snake_case format
 216 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 217 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 220 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 229 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 230 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 9 spaces but found 1
     |         |     space
 230 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 234 | WARNING | [ ] This comment is 60% valid code; is this commented out code?
 235 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 236 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 238 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 241 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 260 | ERROR   | [ ] Variable "pfError" is not in valid snake_case format
 261 | ERROR   | [ ] Variable "pfErrMsg" is not in valid snake_case format
 264 | ERROR   | [ ] Empty line required before block comment
 267 | ERROR   | [ ] You must use "/**" style comments for a function comment
 268 | WARNING | [ ] Found precision alignment of 1 spaces.
 271 | ERROR   | [ ] Empty line required before block comment
 274 | ERROR   | [ ] Function name "pmpro_ipnExit" is not in snake case format, try "pmpro_ipn_exit"
 274 | ERROR   | [ ] You must use "/**" style comments for a function comment
 276 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 281 | ERROR   | [ ] All output should be run through an escaping function (see the Security sections in the
     |         |     WordPress Developer Handbooks), found '$logstr'.
 282 | WARNING | [ ] File operations should use WP_Filesystem methods instead of direct PHP filesystem
     |         |     calls. Found: fopen()
 283 | WARNING | [ ] File operations should use WP_Filesystem methods instead of direct PHP filesystem
     |         |     calls. Found: fwrite()
 284 | WARNING | [ ] File operations should use WP_Filesystem methods instead of direct PHP filesystem
     |         |     calls. Found: fclose()
 289 | ERROR   | [ ] Empty line required before block comment
 292 | ERROR   | [ ] Function name "pmpro_itnChangeMembershipLevel" is not in snake case format, try
     |         |     "pmpro_itn_change_membership_level"
 292 | ERROR   | [ ] You must use "/**" style comments for a function comment
 294 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 296 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 302 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 305 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 311 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 313 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 315 | WARNING | [x] Array double arrow not aligned correctly; expected 9 space(s) between "'user_id'" and
     |         |     double arrow, but found 1.
 316 | WARNING | [x] Array double arrow not aligned correctly; expected 3 space(s) between "'membership_id'"
     |         |     and double arrow, but found 1.
 317 | WARNING | [x] Array double arrow not aligned correctly; expected 9 space(s) between "'code_id'" and
     |         |     double arrow, but found 1.
 319 | WARNING | [x] Array double arrow not aligned correctly; expected 2 space(s) between
     |         |     "'billing_amount'" and double arrow, but found 1.
 320 | WARNING | [x] Array double arrow not aligned correctly; expected 4 space(s) between "'cycle_number'"
     |         |     and double arrow, but found 1.
 321 | WARNING | [x] Array double arrow not aligned correctly; expected 4 space(s) between "'cycle_period'"
     |         |     and double arrow, but found 1.
 322 | WARNING | [x] Array double arrow not aligned correctly; expected 3 space(s) between "'billing_limit'"
     |         |     and double arrow, but found 1.
 323 | WARNING | [x] Array double arrow not aligned correctly; expected 4 space(s) between "'trial_amount'"
     |         |     and double arrow, but found 1.
 324 | WARNING | [x] Array double arrow not aligned correctly; expected 5 space(s) between "'trial_limit'"
     |         |     and double arrow, but found 1.
 325 | WARNING | [x] Array double arrow not aligned correctly; expected 7 space(s) between "'startdate'" and
     |         |     double arrow, but found 1.
 326 | WARNING | [x] Array double arrow not aligned correctly; expected 9 space(s) between "'enddate'" and
     |         |     double arrow, but found 1.
 330 | ERROR   | [ ] All output should be run through an escaping function (see the Security sections in the
     |         |     WordPress Developer Handbooks), found '$pmpro_error'.
 333 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 335 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 336 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 17 spaces but found 1
     |         |     space
 338 | ERROR   | [ ] Processing form data without nonce verification.
 339 | ERROR   | [ ] Detected usage of a non-validated input variable: $_POST
 339 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 339 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 339 | ERROR   | [ ] Processing form data without nonce verification.
 344 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 346 | WARNING | [ ] Usage of a direct database call is discouraged.
 346 | WARNING | [ ] Direct database call without caching detected. Consider using wp_cache_get() /
     |         |     wp_cache_set() or wp_cache_delete().
 346 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found $discount_code_id
 346 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found $morder
 346 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found user_id
 346 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found $morder
 346 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found id
 346 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found current_time
 348 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 349 | ERROR   | [ ] Processing form data without nonce verification.
 352 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 352 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 352 | ERROR   | [ ] Processing form data without nonce verification.
 355 | ERROR   | [ ] Processing form data without nonce verification.
 358 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 358 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 358 | ERROR   | [ ] Processing form data without nonce verification.
 361 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 363 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 369 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 19 spaces but found 1
     |         |     space
 371 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 374 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 377 | WARNING | [ ] This comment is 48% valid code; is this commented out code?
 422 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 428 | ERROR   | [ ] Function name "pmpro_ipnSaveOrder" is not in snake case format, try
     |         |     "pmpro_ipn_save_order"
 428 | ERROR   | [ ] Missing doc comment for function pmpro_ipnSaveOrder()
 430 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 431 | WARNING | [ ] Usage of a direct database call is discouraged.
 431 | WARNING | [ ] Direct database call without caching detected. Consider using wp_cache_get() /
     |         |     wp_cache_set() or wp_cache_delete().
 431 | ERROR   | [ ] Use placeholders and $wpdb->prepare(); found $txn_id
 435 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 436 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 30 spaces but found 1
     |         |     space
 437 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 21 spaces but found 1
     |         |     space
 438 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 15 spaces but found 1
     |         |     space
 439 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 6 spaces but found 1
     |         |     space
 441 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 21 spaces but found 1
     |         |     space
 442 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 9 spaces but found 1
     |         |     space
 443 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 16 spaces but found 1
     |         |     space
 444 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 448 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 449 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 449 | ERROR   | [ ] Use Yoda Condition checks, you must.
 450 | ERROR   | [ ] Object property "InitialPayment" is not in valid snake_case format
 450 | ERROR   | [ ] Detected usage of a non-validated input variable: $_POST
 450 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 450 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 450 | ERROR   | [ ] Processing form data without nonce verification.
 450 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 451 | ERROR   | [ ] Object property "PaymentAmount" is not in valid snake_case format
 451 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 451 | ERROR   | [ ] Detected usage of a non-validated input variable: $_POST
 451 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 451 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 451 | ERROR   | [ ] Processing form data without nonce verification.
 453 | ERROR   | [ ] Object property "FirstName" is not in valid snake_case format
 453 | ERROR   | [ ] Detected usage of a non-validated input variable: $_POST
 453 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 453 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 453 | ERROR   | [ ] Processing form data without nonce verification.
 454 | ERROR   | [ ] Object property "LastName" is not in valid snake_case format
 454 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 454 | ERROR   | [ ] Detected usage of a non-validated input variable: $_POST
 454 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 454 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 454 | ERROR   | [ ] Processing form data without nonce verification.
 455 | ERROR   | [ ] Object property "Email" is not in valid snake_case format
 455 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 5 spaces but found 1
     |         |     space
 455 | ERROR   | [ ] Detected usage of a non-validated input variable: $_POST
 455 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 455 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 455 | ERROR   | [ ] Processing form data without nonce verification.
 456 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 457 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 457 | ERROR   | [ ] Use Yoda Condition checks, you must.
 458 | ERROR   | [ ] Object property "Address1" is not in valid snake_case format
 458 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 9 spaces but found 1
     |         |     space
 459 | ERROR   | [ ] Object property "City" is not in valid snake_case format
 459 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 13 spaces but found 1
     |         |     space
 460 | ERROR   | [ ] Object property "State" is not in valid snake_case format
 460 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 12 spaces but found 1
     |         |     space
 461 | ERROR   | [ ] Object property "CountryCode" is not in valid snake_case format
 461 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 6 spaces but found 1
     |         |     space
 462 | ERROR   | [ ] Object property "Zip" is not in valid snake_case format
 462 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 14 spaces but found 1
     |         |     space
 463 | ERROR   | [ ] Object property "PhoneNumber" is not in valid snake_case format
 463 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 6 spaces but found 1
     |         |     space
 464 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 4 spaces but found 1
     |         |     space
 464 | ERROR   | [ ] Detected usage of a non-validated input variable: $_POST
 464 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 464 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 464 | ERROR   | [ ] Processing form data without nonce verification.
 464 | ERROR   | [ ] Detected usage of a non-validated input variable: $_POST
 464 | ERROR   | [ ] Missing wp_unslash() before sanitization.
 464 | ERROR   | [ ] Detected usage of a non-sanitized input variable: $_POST
 464 | ERROR   | [ ] Processing form data without nonce verification.
 465 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 466 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 4 spaces but found 1
     |         |     space
 467 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 3 spaces but found 1
     |         |     space
 468 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 5 spaces but found 1
     |         |     space
 470 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 3 spaces but found 1
     |         |     space
 471 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 472 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 14 spaces but found 1
     |         |     space
 473 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 9 spaces but found 1
     |         |     space
 474 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 7 spaces but found 1
     |         |     space
 475 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 8 spaces but found 1
     |         |     space
 476 | ERROR   | [ ] Object property "ExpirationDate" is not in valid snake_case format
 476 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 8 spaces but found 1
     |         |     space
 477 | ERROR   | [ ] Object property "ExpirationDate_YdashM" is not in valid snake_case format
 479 | WARNING | [ ] This comment is 52% valid code; is this commented out code?
 482 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 485 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 497 | ERROR   | [ ] Doc comment short description must start with a capital letter
 501 | ERROR   | [ ] Function name "pmpro_pfGetData" is not in snake case format, try "pmpro_pf_get_data"
 502 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 503 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 503 | ERROR   | [ ] Processing form data without nonce verification.
 504 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 505 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 506 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 508 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 509 | ERROR   | [ ] The use of function sizeof() is forbidden; use count() instead
 509 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 509 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 512 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 515 | ERROR   | [ ] Doc comment for parameter "$pfData" missing
 515 | ERROR   | [ ] Doc comment for parameter "$pfParamString" missing
 515 | ERROR   | [ ] Doc comment for parameter "$passPhrase" missing
 516 | ERROR   | [ ] Doc comment short description must start with a capital letter
 520 | ERROR   | [ ] Function name "pmpro_pfValidSignature" is not in snake case format, try
     |         |     "pmpro_pf_valid_signature"
 520 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 520 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 520 | ERROR   | [ ] Variable "passPhrase" is not in valid snake_case format
 521 | WARNING | [ ] Found precision alignment of 1 spaces.
 521 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 522 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 523 | WARNING | [ ] Found: !=. Use strict comparisons (=== or !==).
 523 | ERROR   | [ ] Use Yoda Condition checks, you must.
 524 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 524 | WARNING | [ ] urlencode() should only be used when dealing with legacy applications rawurlencode()
     |         |     should now be used instead. See http://php.net/manual/en/function.rawurlencode.php and
     |         |     http://www.faqs.org/rfcs/rfc3986.html
 529 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 530 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 530 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 532 | ERROR   | [ ] Variable "passPhrase" is not in valid snake_case format
 533 | ERROR   | [ ] Variable "tempParamString" is not in valid snake_case format
 533 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 535 | ERROR   | [ ] Variable "tempParamString" is not in valid snake_case format
 535 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 535 | WARNING | [ ] urlencode() should only be used when dealing with legacy applications rawurlencode()
     |         |     should now be used instead. See http://php.net/manual/en/function.rawurlencode.php and
     |         |     http://www.faqs.org/rfcs/rfc3986.html
 535 | ERROR   | [ ] Variable "passPhrase" is not in valid snake_case format
 537 | ERROR   | [ ] Variable "tempParamString" is not in valid snake_case format
 538 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 4 spaces but found 1
     |         |     space
 538 | ERROR   | [ ] Variable "pfData" is not in valid snake_case format
 538 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 543 | ERROR   | [ ] Doc comment for parameter "$pfHost" missing
 543 | ERROR   | [ ] Doc comment for parameter "$pfParamString" missing
 543 | ERROR   | [ ] Doc comment for parameter "$pfProxy" missing
 544 | ERROR   | [ ] Doc comment short description must start with a capital letter
 547 | ERROR   | [ ] Missing parameter name
 548 | ERROR   | [ ] Missing parameter name
 549 | ERROR   | [ ] Missing parameter name
 551 | ERROR   | [ ] Function name "pmpro_pfValidData" is not in snake case format, try
     |         |     "pmpro_pf_valid_data"
 551 | ERROR   | [ ] Variable "pfHost" is not in valid snake_case format
 551 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 551 | ERROR   | [ ] Variable "pfProxy" is not in valid snake_case format
 552 | WARNING | [ ] Found precision alignment of 1 spaces.
 552 | ERROR   | [ ] Variable "pfHost" is not in valid snake_case format
 553 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 554 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 556 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 557 | ERROR   | [ ] Variable "pfHost" is not in valid snake_case format
 558 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 559 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 561 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 562 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 562 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 563 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 563 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 564 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 564 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 565 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 566 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 567 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 568 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 569 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 570 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 570 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 571 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 572 | ERROR   | [ ] Variable "pfProxy" is not in valid snake_case format
 573 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 575 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 576 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 577 | WARNING | [ ] Using cURL functions is highly discouraged. Use wp_remote_get() instead.
 579 | ERROR   | [ ] Expected 1 space after closing brace; newline found
 580 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 582 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 583 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 5 spaces but found 1
     |         |     space
 584 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 8 spaces but found 1
     |         |     space
 585 | ERROR   | [ ] Variable "headerDone" is not in valid snake_case format
 586 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 587 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 2 spaces but found 1
     |         |     space
 588 | ERROR   | [ ] Variable "pfHost" is not in valid snake_case format
 591 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 592 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 593 | WARNING | [ ] File operations should use WP_Filesystem methods instead of direct PHP filesystem
     |         |     calls. Found: fsockopen()
 593 | ERROR   | [ ] Variable "pfHost" is not in valid snake_case format
 594 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 595 | ERROR   | [ ] Variable "pfParamString" is not in valid snake_case format
 596 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 599 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 600 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 601 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 602 | ERROR   | [ ] Variable "headerDone" is not in valid snake_case format
 603 | ERROR   | [ ] Expected 1 space after closing brace; newline found
 604 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 605 | WARNING | [x] Usage of ELSE IF is discouraged; use ELSEIF instead
 605 | ERROR   | [ ] Variable "headerDone" is not in valid snake_case format
 606 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 612 | WARNING | [ ] print_r() found. Debug code should not normally be used in production.
 613 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 614 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 8 spaces but found 1
     |         |     space
 615 | ERROR   | [ ] Variable "verifyResult" is not in valid snake_case format
 616 | ERROR   | [ ] Variable "verifyResult" is not in valid snake_case format
 616 | WARNING | [ ] Found: ==. Use strict comparisons (=== or !==).
 622 | ERROR   | [ ] Doc comment for parameter "$sourceIP" missing
 623 | ERROR   | [ ] Doc comment short description must start with a capital letter
 626 | ERROR   | [ ] Missing parameter name
 628 | ERROR   | [ ] Function name "pmpro_pfValidIP" is not in snake case format, try "pmpro_pf_valid_i_p"
 628 | ERROR   | [ ] Variable "sourceIP" is not in valid snake_case format
 629 | WARNING | [ ] Found precision alignment of 1 spaces.
 629 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 630 | ERROR   | [ ] Variable "validHosts" is not in valid snake_case format
 636 | ERROR   | [ ] Variable "validIps" is not in valid snake_case format
 636 | WARNING | [x] Equals sign not aligned with surrounding assignments; expected 3 spaces but found 1
     |         |     space
 637 | ERROR   | [ ] Variable "validHosts" is not in valid snake_case format
 637 | ERROR   | [ ] Variable "pfHostname" is not in valid snake_case format
 638 | ERROR   | [ ] Variable "pfHostname" is not in valid snake_case format
 639 | ERROR   | [ ] Use Yoda Condition checks, you must.
 640 | ERROR   | [ ] Variable "validIps" is not in valid snake_case format
 640 | ERROR   | [ ] Variable "validIps" is not in valid snake_case format
 643 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or question marks
 644 | ERROR   | [ ] Variable "validIps" is not in valid snake_case format
 644 | ERROR   | [ ] Variable "validIps" is not in valid snake_case format
 645 | WARNING | [ ] print_r() found. Debug code should not normally be used in production.
 645 | ERROR   | [ ] Variable "validIps" is not in valid snake_case format
 646 | WARNING | [ ] Not using strict comparison for in_array; supply true for third argument.
 646 | ERROR   | [ ] Variable "sourceIP" is not in valid snake_case format
 646 | ERROR   | [ ] Variable "validIps" is not in valid snake_case format
 652 | ERROR   | [ ] Doc comment for parameter "$amount1" missing
 652 | ERROR   | [ ] Doc comment for parameter "$amount2" missing
 653 | ERROR   | [ ] Doc comment short description must start with a capital letter
 662 | ERROR   | [ ] Missing parameter name
 663 | ERROR   | [ ] Missing parameter name
 665 | ERROR   | [ ] Function name "pmpro_pfAmountsEqual" is not in snake case format, try
     |         |     "pmpro_pf_amounts_equal"
-------------------------------------------------------------------------------------------------------------
PHPCBF CAN FIX THE 64 MARKED SNIFF VIOLATIONS AUTOMATICALLY
-------------------------------------------------------------------------------------------------------------
