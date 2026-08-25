from playwright.sync_api import sync_playwright

def verify():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        page.goto("http://localhost:8000/", wait_until="networkidle")
        page.screenshot(path="/home/jules/verification/screenshots/dashboard_all.png")

        # Click on Patients tab if available or navigate
        try:
            page.click("text=Patients")
            page.wait_for_timeout(1000)
            page.screenshot(path="/home/jules/verification/screenshots/patients_all.png")
        except Exception as e:
            print("Patients tab click error:", e)

        # Click on Calendar tab
        try:
            page.click("text=Calendar")
            page.wait_for_timeout(1000)
            page.screenshot(path="/home/jules/verification/screenshots/calendar_all.png")
        except Exception as e:
            print("Calendar tab click error:", e)

        # Click on Billing tab
        try:
            page.click("text=Billing")
            page.wait_for_timeout(1000)
            page.screenshot(path="/home/jules/verification/screenshots/billing_all.png")
        except Exception as e:
            print("Billing tab click error:", e)

        browser.close()

if __name__ == "__main__":
    verify()
