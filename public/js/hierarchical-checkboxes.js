/**

Hierarchical Checkboxes

author: Anil Maharjan

USAGE:

Template Construction:
User ROOT template and Nest as many NODE templates 

ROOT:

<div class="hierarchy-checkboxes" rel="test">
  <input class="hierarchy-root-checkbox" type="checkbox">
  <label class="hierarchy-root-label">Root</label>
  <div class="hierarchy-root-child hierarchy-node">
   {{ NODE TEMPLATE HERE }}
  </div>
</div>

NODE:

<div class="hierarchy-node [leaf]">
  <input class="hierarchy-checkbox" type="checkbox">
  <label class="hierarchy-label">[Title]</label>
  {{ NODE TEMPLATE HERE }}
</div> 



// Basic Example Template
<div class="hierarchy-checkboxes" rel="test">
  <input class="hierarchy-root-checkbox" type="checkbox">
  <label class="hierarchy-root-label">Root</label>
  <div class="hierarchy-root-child hierarchy-node">
   <div class="hierarchy-node leaf">
      <input class="hierarchy-checkbox" type="checkbox">
      <label class="hierarchy-label">Markets</label>
      <div class="hierarchy-node leaf">
        <input class="hierarchy-checkbox" type="checkbox">
        <label class="hierarchy-label">Markets</label>
      </div> 
    </div> 
    <div class="hierarchy-node leaf">
      <input class="hierarchy-checkbox" type="checkbox">
      <label class="hierarchy-label">Markets</label>
    </div> 
  </div>
</div>


API:

EVENTS:

1. checkboxesUpdate:
  Triggers whenever the check/uncheck tasks complete withing the hierarchical checkboxes

Example:
jQuery('.hierarchy-checkboxes[rel=IDENTIFIER]').on('checkboxesUpdate',function(){
  console.log("Changed!");
});


**/

jQuery(document).ready(function () {
  jQuery(".hierarchy-checkboxes .hierarchy-root-child div div").hide();
  jQuery(".hierarchy-checkboxes .hierarchy-root-child")
    .attr("rel", function () {
      const $this = jQuery(this);
      $this.attr("rel", $this.closest(".hierarchy-checkboxes").attr("rel"));
    })
    .appendTo("body")
    .hide();
  jQuery(
    ".hierarchy-checkboxes, .hierarchy-root-child .hierarchy-node"
  ).prepend('<div class="expand-collapse-button"></div>');
  // Root label toggles root-child / Popup layer as a whole
  jQuery(".hierarchy-root-label").click(function () {
    const $this = jQuery(this);
    const $thisNode = $this.parent();
    const rel = $thisNode.attr("rel");
    const $rootChild = jQuery(".hierarchy-root-child[rel=" + rel + "]");
    if (!$thisNode.hasClass("child-expanded")) {
      $thisNode.addClass("child-expanded");
      const thisPos = $thisNode.offset();

      $rootChild
        .css({ left: thisPos.left, top: thisPos.top + $thisNode.height() - 1 })
        .slideDown("fast");
    } else {
      $rootChild.slideUp("fast", function () {
        $thisNode.removeClass("child-expanded");
      });
    }
  });

  jQuery(".expand-collapse-button").click(function () {
    jQuery(this).siblings(".hierarchy-label, .hierarchy-root-label").click();
  });
  jQuery(".hierarchy-root-checkbox").change(function () {
    const $this = jQuery(this);
    //$thisNode is parent to current checkbox so it would represent current level node
    const $thisNode = $this.parent();

    const rel = $thisNode.attr("rel"); // Identifier (rel attribute of current hierarchy root)
    const $rootChild = jQuery(".hierarchy-root-child[rel=" + rel + "]"); // The node that contains all the elements of hierarchy;
    $rootChild
      .find("input.hierarchy-checkbox")
      .prop("checked", $this.prop("checked"));
    $thisNode.trigger("checkboxesUpdate");
  });

  // Each node's label toggles the node's child / label's sibling
  jQuery(".hierarchy-node .hierarchy-label").click(function () {
    const $this = jQuery(this);
    const $thisNode = $this.parent();
    if (!$thisNode.hasClass("child-expanded")) {
      $thisNode.addClass("child-expanded");
      $this.siblings(".hierarchy-node").slideDown("fast");
    } else {
      $this.siblings(".hierarchy-node").slideUp("fast", function () {
        $thisNode.removeClass("child-expanded");
      });
    }
  });

  // Each node's checkbox toggles the node's child / checkbox's sibling
  jQuery(".hierarchy-node .hierarchy-checkbox").change(function () {
    const $this = jQuery(this);
    //$thisNode is parent to current checkbox so it would represent current level node
    const $thisNode = $this.parent();
    const $parentNode = $thisNode.parent(".hierarchy-node");
    const $parentNodeCheckbox = $parentNode.children(
      "input.hierarchy-checkbox"
    );

    const $rootChild = $this.parents(".hierarchy-root-child"); // The node that contains all the elements of hierarchy;
    const rel = $rootChild.attr("rel"); // Identifier (rel attribute of current hierarchy root)
    const $root = jQuery(".hierarchy-checkboxes[rel=" + rel + "]");
    const $rootCheckbox = jQuery(
      ".hierarchy-checkboxes[rel=" + rel + "] .hierarchy-root-checkbox"
    );

    // take care of children | Easy one
    $this
      .siblings(".hierarchy-node")
      .find("input.hierarchy-checkbox")
      .prop("checked", $this.prop("checked"));

    //take care of parents | tough one
    if (!$this.prop("checked")) {
      // If unchecked uncheck all the ancestors
      $thisNode
        .parents(".hierarchy-node")
        .children("input.hierarchy-checkbox")
        .prop("checked", $this.prop("checked"));
      // also uncheck the root
      $rootCheckbox.prop("checked", false);
    } else {
      // If checked check for the siblings state and check the parent if all siblings are checked too
      const allCheckboxesInCurrentDepth = $parentNode.find(
        ".hierarchy-node .hierarchy-checkbox"
      ).length;
      const allCheckedCheckboxesInCurrentDepth = $parentNode.find(
        ".hierarchy-node .hierarchy-checkbox:checked"
      ).length;
      // all nodes in and below siblings are checked
      if (allCheckboxesInCurrentDepth === allCheckedCheckboxesInCurrentDepth) {
        // check the parent
        if ($parentNodeCheckbox.length) {
          $parentNodeCheckbox.prop("checked", true);
        } else {
          $rootCheckbox.prop("checked", true);
        }
      }
    }
    $root.trigger("checkboxesUpdate", [
      $rootChild.find(".hierarchy-checkbox:checked")
    ]);
  });
});
