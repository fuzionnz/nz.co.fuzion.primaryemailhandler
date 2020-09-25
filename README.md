# nz.co.fuzion.primaryemailhandler

Customisation added for the below usecase

Contact has Email E1 (primary), E2 & E3.

- If E1 is on hold. Add customization so that when E1 becomes on hold, primary flag is removed from E1 and E2 becomes a new primary email.

- If there is only 1 email with primary + on hold and
   - An update is made to E1 value - remove on hold from E1.
   - New email E2 is added - make E2 as primary and let E1 remain on hold.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM (*FIXME: Version number*)

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl nz.co.fuzion.primaryemailhandler@https://github.com/FIXME/nz.co.fuzion.primaryemailhandler/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/nz.co.fuzion.primaryemailhandler.git
cv en primaryemailhandler
```

## Usage

(* FIXME: Where would a new user navigate to get started? What changes would they see? *)

## Known Issues

(* FIXME *)
