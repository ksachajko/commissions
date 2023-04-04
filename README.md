# Commissions #
Calculates commission fees for transactions

## Running ##
The application itself is located in the `app` folder.
Docker was added for author's convenience.

### To run outside docker: ###
Install dependencies

Run `app.php` script located `app/src/` folder

Source list of transactions may be found in `src/resources/input.txt`

## Commands
Code style dry run
`composer run-script cs-fixer-dry-run`

Code style check and fix
`composer run-script cs-fixer`

Unit Tests
`composer run-script phpunit`