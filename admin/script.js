async function decodeGoogleDocGrid(docUrl) {
    try {
      // Fetch the document content
      const response = await fetch(docUrl);
      if (!response.ok) {
        console.error("Failed to fetch the document.");
        return;
      }
  
      const html = await response.text();
  
      // Parse the HTML content to extract the table data
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, "text/html");
      const table = doc.querySelector("table");
  
      if (!table) {
        console.error("No table found in the document.");
        return;
      }
  
      const rows = table.querySelectorAll("tr");
      const data = [];
      let maxX = 0;
      let maxY = 0;
  
      // Skip the header row and process the remaining rows
      for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].querySelectorAll("td");
        if (cells.length < 3) continue;
  
        const x = parseInt(cells[0].textContent.trim());
        const char = cells[1].textContent.trim();
        const y = parseInt(cells[2].textContent.trim());
  
        if (isNaN(x) || isNaN(y) || !char) continue;
  
        data.push({ char, x, y });
        if (x > maxX) maxX = x;
        if (y > maxY) maxY = y;
      }
  
      // Initialize the grid with spaces
      const grid = Array.from({ length: maxY + 1 }, () =>
        Array(maxX + 1).fill(" ")
      );
  
      // Populate the grid with characters
      for (let { char, x, y } of data) {
        grid[y][x] = char;
      }
  
      // Print the grid row by row
      for (let row of grid) {
        console.log(row.join(""));
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }
  