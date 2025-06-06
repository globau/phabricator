<?php

final class PhabricatorDiffusionConfigOptions
  extends PhabricatorApplicationConfigOptions {

  public function getName() {
    return pht('Diffusion');
  }

  public function getDescription() {
    return pht('Configure Diffusion repository browsing.');
  }

  public function getIcon() {
    return 'fa-code';
  }

  public function getGroup() {
    return 'apps';
  }

  public function getOptions() {
    $custom_field_type = 'custom:PhabricatorCustomFieldConfigOptionType';

    $fields = array(
      new PhabricatorCommitRepositoryField(),
      new PhabricatorCommitBranchesField(),
      new PhabricatorCommitTagsField(),
      new PhabricatorCommitMergedCommitsField(),
    );

    $default_fields = array();
    foreach ($fields as $field) {
      $default_fields[$field->getFieldKey()] = array(
        'disabled' => $field->shouldDisableByDefault(),
      );
    }

    return array(
      $this->newOption(
        'metamta.diffusion.attach-patches',
        'bool',
        false)
        ->setBoolOptions(
          array(
            pht('Attach Patches'),
            pht('Do Not Attach Patches'),
          ))
        ->setDescription(
          pht(
            'Set this to true if you want patches to be attached to commit '.
            'notifications from Diffusion.')),
      $this->newOption('metamta.diffusion.inline-patches', 'int', 0)
        ->setSummary(pht('Include patches in Diffusion mail as body text.'))
        ->setDescription(
          pht(
            'To include patches in Diffusion email bodies, set this to a '.
            'positive integer. Patches will be inlined if they are at most '.
            'that many lines. By default, patches are not inlined.')),
      $this->newOption('metamta.diffusion.byte-limit', 'int', 1024 * 1024)
        ->setDescription(pht('Hard byte limit on including patches in email.')),
      $this->newOption('metamta.diffusion.time-limit', 'int', 60)
        ->setDescription(pht('Hard time limit on generating patches.')),

      $this->newOption(
        'audit.can-author-close-audit',
        'bool',
        false)
        ->setBoolOptions(
          array(
            pht('Enable Self-Accept'),
            pht('Disable Self-Accept'),
          ))
        ->setDescription(
          pht(
            'Allows the author of a commit to be an auditor and accept their '.
            'own commits. Note that this behavior is different from the '.
            'behavior implied by the name of the option: long ago, it did '.
            'something else.')),

      $this->newOption('bugtraq.url', 'string', null)
        ->addExample('https://bugs.php.net/%BUGID%', pht('PHP bugs'))
        ->addExample('/%BUGID%', pht('Local Maniphest URL'))
        ->setDescription(
          pht(
            'URL of external bug tracker used by Diffusion. %s will be '.
            'substituted by the bug ID.',
            '%BUGID%')),
      $this->newOption('bugtraq.logregex', 'list<regex>', array())
        ->addExample(array('/\B#([1-9]\d*)\b/'), pht('Issue #123'))
        ->addExample(
          array('/[Ii]ssues?:?(\s*,?\s*#\d+)+/', '/(\d+)/'),
          pht('Issue #123, #456'))
        ->addExample(array('/(?<!#)\b(T[1-9]\d*)\b/'), pht('Task T123'))
        ->addExample('/[A-Z]{2,}-\d+/', pht('JIRA-1234'))
        ->setDescription(
          pht(
            'Regular expression to link external bug tracker. See '.
            'http://tortoisesvn.net/docs/release/TortoiseSVN_en/'.
            'tsvn-dug-bugtracker.html for further explanation.')),
      $this->newOption('diffusion.allow-http-auth', 'bool', false)
        ->setBoolOptions(
          array(
            pht('Allow HTTP Basic Auth'),
            pht('Disallow HTTP Basic Auth'),
          ))
        ->setSummary(pht('Enable HTTP Basic Auth for repositories.'))
        ->setDescription(
          pht(
            "This server can serve repositories over HTTP, using HTTP basic ".
            "auth.\n\n".
            "Because HTTP basic auth is less secure than SSH auth, it is ".
            "disabled by default. You can enable it here if you'd like to use ".
            "it anyway. There's nothing fundamentally insecure about it as ".
            "long as this server uses HTTPS, but it presents a much lower ".
            "barrier to attackers than SSH does.\n\n".
            "Consider using SSH for authenticated access to repositories ".
            "instead of HTTP.")),
      $this->newOption('diffusion.allow-git-lfs', 'bool', false)
        ->setBoolOptions(
          array(
            pht('Allow Git LFS'),
            pht('Disallow Git LFS'),
          ))
        ->setLocked(true)
        ->setSummary(pht('Allow Git Large File Storage (LFS).'))
        ->setDescription(
          pht(
            'This server supports Git LFS, a Git extension for storing large '.
            'files alongside a repository. Activate this setting to allow '.
            'the extension to store file data.')),
      $this->newOption('diffusion.ssh-user', 'string', null)
        ->setLocked(true)
        ->setSummary(pht('Login username for SSH connections to repositories.'))
        ->setDescription(
          pht(
            'When constructing clone URIs to show to users, Diffusion will '.
            'fill in this login username. If you have configured a VCS user '.
            'like `git`, you should provide it here.')),
      $this->newOption('diffusion.ssh-port', 'int', null)
        ->setLocked(true)
        ->setSummary(pht('Port for SSH connections to repositories.'))
        ->setDescription(
          pht(
            'When constructing clone URIs to show to users, Diffusion by '.
            'default will not display a port assuming the default for your '.
            'VCS. Explicitly declare when running on a non-standard port.')),
      $this->newOption('diffusion.ssh-host', 'string', null)
        ->setLocked(true)
        ->setSummary(pht('Host for SSH connections to repositories.'))
        ->setDescription(
          pht(
            'If you accept SSH traffic on a different host from web traffic '.
            '(for example, if you use different SSH and web load balancers), '.
            'you can set the SSH hostname here. This is an advanced option.')),
      $this->newOption('diffusion.fields', $custom_field_type, $default_fields)
        ->setCustomData(
          id(new PhabricatorRepositoryCommit())
            ->getCustomFieldBaseClass())
        ->setDescription(
          pht('Select and reorder Diffusion fields.')),
      $this->newOption('diffusion.shallow-repos', 'wild', null)
        ->setSummary(pht('Callsigns for repos to fetch shallowly.'))
        ->setDescription(
          pht(
            'JSON with config options, e.g., {"<callsign>": "<isoformat date cutoff>", ...}')),
      $this->newOption('diffusion.legacy-repos-mapping', 'wild', null)
        ->setSummary(pht('Mapping of callsigns of legacy and new repos.'))
        ->setDescription(
          pht(
            'JSON with config options, e.g., {"<legacy_repo_callsign>": "<new_repo_callsign>", ...}')),
    );
  }

}
