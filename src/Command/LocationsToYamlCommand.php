<?php

namespace Drupal\middlebury_event_sync\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Yaml;
// @codingStandardsIgnoreLine
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Core\Command\Shared\CommandTrait;
use Drupal\Console\Core\Style\DrupalStyle;

/**
 * Class LocationsToYamlCommand.
 *
 * @package Drupal\middlebury_event_sync
 *
 * @DrupalCommand (
 *     extension="middlebury_event_sync",
 *     extensionType="module"
 * )
 */
class LocationsToYamlCommand extends Command {

  use CommandTrait;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('middlebury_event_sync:locations_to_yaml')
      ->setDescription($this->trans('commands.middlebury_event_sync.locations_to_yaml.description'))
      ->addArgument(
          'target',
          InputArgument::REQUIRED,
          $this->trans('commands.middlebury_event_sync.locations_to_yaml.arguments.target'),
          NULL
        )
      ->addArgument(
          'r25_location_xml',
          InputArgument::REQUIRED,
          $this->trans('commands.middlebury_event_sync.locations_to_yaml.arguments.r25_location_xml'),
          NULL
        );
  }

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    $target = $input->getArgument('target');
    if (!file_exists($target)) {
      $create_target = $io->confirm(
        sprintf($this->trans('commands.middlebury_event_sync.locations_to_yaml.arguments.create_target'), $target),
        FALSE
      );
      if (!$create_target) {
        throw new \RuntimeException($this->trans('commands.middlebury_event_sync.locations_to_yaml.errors.abort'));
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    // Validate our target file.
    $target = $input->getArgument('target');
    if (!file_exists($target)) {
      touch($target);
    }
    $target_info = new \SplFileInfo($input->getArgument('target'));
    if (!$target_info->isWritable()) {
      throw new \RuntimeException(sprintf($this->trans('commands.middlebury_event_sync.locations_to_yaml.errors.not_writable'), $target));
    }
    if ($target_info->isDir()) {
      throw new \RuntimeException(sprintf($this->trans('commands.middlebury_event_sync.locations_to_yaml.errors.is_directory'), $target));
    }

    // Validate the R25 location XML.
    $r25_location_xml = $input->getArgument('r25_location_xml');
    if (!file_exists($r25_location_xml)) {
      throw new \RuntimeException(sprintf($this->trans('commands.middlebury_event_sync.locations_to_yaml.errors.does_not_exist'), $r25_location_xml));
    }

    $locations = [];

    // Run through the locations.
    $doc = new \DomDocument();
    $doc->load($r25_location_xml);
    $xpath = new \DOMXPath($doc);
    $xpath->registerNamespace('o', 'urn:schemas-microsoft-com:office:office');
    $xpath->registerNamespace('x', 'urn:schemas-microsoft-com:office:excel');
    $xpath->registerNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
    $worksheet = $xpath->query('//ss:Worksheet[@ss:Name="Location List"]')->item(0);
    $rows = $xpath->query('ss:Table/ss:Row', $worksheet);
    $headings = $rows->item(0);
    $code_cell = $this->getCellFor('Location Name', $headings, $xpath);
    $formal_name_cell = $this->getCellFor('Location Formal Name', $headings, $xpath);
    for ($i = 1; $i < $rows->length; $i++) {
      $row = $rows->item($i);
      $cell_data = $xpath->query('ss:Cell/ss:Data', $row);
      $code = $cell_data->item($code_cell)->nodeValue;
      $location = [
        'entity' => 'taxonomy_term',
        'vid' => 'locations',
        'name' => $cell_data->item($formal_name_cell)->nodeValue,
        'field_location_code' => $code,
        'field_building_code' => preg_replace('/^(\w+)\s+.+$/', '\1', $code),
      ];
      $locations[] = $location;
    }

    file_put_contents($target, Yaml::dump($locations));
    $io->info($this->trans('commands.middlebury_event_sync.locations_to_yaml.messages.success'));
  }

  /**
   * Gets a cell from a spreadsheet.
   *
   * @param string $heading
   *   The column heading.
   * @param \DOMNode $headings
   *   Each of the heading we want to search for.
   * @param \DOMXPath $xpath
   *   An xpath query used for the search.
   *
   * @return \DOMNode
   *   A cell from a spreadsheet.
   */
  protected function getCellFor($heading, \DOMNode $headings, \DOMXPath $xpath) {
    $i = 0;
    foreach ($xpath->query('ss:Cell/ss:Data', $headings) as $column) {
      if ($column->nodeValue == $heading) {
        return $i;
      }
      $i++;
    }
    throw new \Exception(sprintf(
      $this->trans('commands.middlebury_event_sync.locations_to_yaml.errors.heading_not_found'),
      $heading));
  }

}
