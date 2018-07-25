jQuery('.composite_data').on('wc-composite-initializing', (event, composite) => {
  composite.actions.add_action('component_summary_content_updated', () => {
    jQuery('.quantity').addQty()
  }, 100)
  composite.actions.add_action('component_selection_changed', () => {
    jQuery('.quantity').addQty()
  }, 100)
})
