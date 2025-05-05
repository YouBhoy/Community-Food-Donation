document.addEventListener("DOMContentLoaded", () => {
  // Sidebar Toggle
  const sidebarToggle = document.getElementById("sidebar-toggle")
  const sidebarClose = document.getElementById("sidebar-close")
  const sidebar = document.getElementById("sidebar")

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.style.transform = "translateX(0)"
    })
  }

  if (sidebarClose) {
    sidebarClose.addEventListener("click", () => {
      sidebar.style.transform = ""
    })
  }

  // Navigation
  const navLinks = document.querySelectorAll(".sidebar-link")
  const contentSections = document.querySelectorAll(".content")

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()

      // Remove active class from all links
      navLinks.forEach((link) => link.classList.remove("active"))

      // Add active class to clicked link
      this.classList.add("active")

      // Hide all content sections
      contentSections.forEach((section) => section.classList.add("hidden"))

      // Show the corresponding content section
      const targetId = this.getAttribute("href").substring(1) + "-content"
      const targetContent = document.getElementById(targetId)
      if (targetContent) {
        targetContent.classList.remove("hidden")
      }
    })
  })

  // Tab Functionality
  const setupTabs = (tabsContainer) => {
    if (!tabsContainer) return

    const tabButtons = tabsContainer.querySelectorAll(".tab-button")

    tabButtons.forEach((button) => {
      button.addEventListener("click", function () {
        // Get the tab value
        const tabValue = this.getAttribute("data-tab")

        // Remove active class from all buttons in this container
        tabsContainer.querySelectorAll(".tab-button").forEach((btn) => {
          btn.classList.remove("active")
        })

        // Add active class to clicked button
        this.classList.add("active")

        // Hide all tab content in this container
        const tabContents = document.querySelectorAll(`#${tabValue}-tab`)

        // First hide all related tab contents
        const allTabContents = document.querySelectorAll(".tab-content")
        allTabContents.forEach((content) => {
          if (content.id.includes("-tab")) {
            content.classList.remove("active")
          }
        })

        // Then show the selected tab content
        tabContents.forEach((content) => {
          if (content) {
            content.classList.add("active")
          }
        })
      })
    })
  }

  // Setup all tab containers
  document.querySelectorAll(".tabs").forEach(setupTabs)

  // Search functionality
  const searchInputs = document.querySelectorAll(".search-input")
  searchInputs.forEach((input) => {
    input.addEventListener("keyup", function () {
      const value = this.value.toLowerCase()
      const tableId = this.id.replace("search-", "")
      const tableRows = document.querySelectorAll(`#${tableId}-tab table tbody tr`)

      tableRows.forEach((row) => {
        const text = row.textContent.toLowerCase()
        row.style.display = text.includes(value) ? "" : "none"
      })
    })
  })
})
